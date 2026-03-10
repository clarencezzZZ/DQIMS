# Section-Wide FIFO Queue Implementation

## Overview

This document describes the implementation of **First-Come, First-Serve (FIFO) per section** with priority alternation in the DQIMS queue management system.

## Problem Statement

Previously, the queue system operated on a **per-category** (service type) basis. This meant:
- Each service type had its own independent queue
- Multiple "Serve" buttons could be enabled simultaneously within the same section
- No unified FIFO enforcement across different service types in the same section

### Example of Previous Behavior (INCORRECT):
```
AGGREGATE AND CORRECTION SECTION
├─ CANCELATION (DAR) Queue:
│  └─ ACS-001 (NORMAL) ← Can serve
├─ AMENDMENT Queue:
│  └─ ACS-002 (NORMAL) ← Can also serve! ❌
└─ OTHER SERVICE Queue:
   └─ ACS-003 (PRIORITY) ← Can also serve! ❌
```

## Solution Implemented

The system now implements **section-wide FIFO** with the following rules:

### Core Rules

1. **Unified Section Queue**: All categories (service types) within the same section share one unified queue
2. **First-Come, First-Serve**: Only the earliest waiting inquiry in the section can be served
3. **Priority Alternation**: Serving order alternates NORMAL → PRIORITY → NORMAL → PRIORITY
4. **Section Independence**: Each section operates independently
5. **Backend Enforcement**: Queue order is enforced at both UI and backend levels

### New Behavior (CORRECT):
```
AGGREGATE AND CORRECTION SECTION (UNIFIED QUEUE)
├─ ACS-001 (NORMAL, 08:00 AM) ← Served first
├─ ACS-002 (PRIORITY, 08:05 AM) ← Next to serve ✅
├─ ACS-003 (NORMAL, 08:10 AM) ← Disabled ⛔
└─ ACS-004 (NORMAL, 08:15 AM) ← Disabled ⛔
```

## Files Modified

### 1. `app/Http/Controllers/SectionController.php`

**Method:** `getNextInquiryByPriority($categoryId)`

**Changes:**
- Changed from category-based to section-based querying
- Uses database JOIN to filter by section instead of category_id
- Maintains priority alternation logic across the entire section

**Key Code Changes:**
```php
// OLD: Per-category query
$waitingInquiries = Inquiry::today()
    ->byCategory($categoryId)
    ->waiting()
    ->orderBy('created_at')
    ->get();

// NEW: Section-wide query
$waitingInquiries = Inquiry::today()
    ->join('categories', 'inquiries.category_id', '=', 'categories.id')
    ->where('categories.section', $section)
    ->where('inquiries.status', 'waiting')
    ->select('inquiries.*')
    ->orderBy('inquiries.created_at')
    ->get();
```

### 2. `app/Http/Controllers/AdminController.php`

**Method 1:** `getNextInquiryByPriorityForAdmin($categoryId)`

**Changes:**
- Same section-wide transformation as SectionController
- Returns next inquiry based on section, not individual category

**Method 2:** `updateInquiryStatus(Request $request)`

**Changes:**
- Enhanced validation message to mention section-wide FIFO
- Provides clearer feedback about queue order enforcement

**Method 3:** `inquiries(Request $request)`

**Changes:**
- Modified `$nextInquiries` population to work by section instead of category
- Prevents duplicate processing of sections

**Key Code Changes:**
```php
// Get next inquiry for each SECTION (not category)
$nextInquiries = [];
$processedSections = [];

foreach ($categories as $category) {
    $section = $category->section;
    
    if (!isset($processedSections[$section])) {
        $nextInquiry = $this->getNextInquiryByPriorityForAdmin($category->id);
        if ($nextInquiry) {
            $nextInquiries[$section] = $nextInquiry->id;
        }
        $processedSections[$section] = true;
    }
}
```

### 3. `resources/views/admin/inquiries.blade.php`

**Changes:**
- Updated `$isNext` check to use section instead of category_id
- Enhanced warning message to explain section-wide FIFO

**Key Code Changes:**
```blade
@php
    // Check by section (not category) since we now have section-wide FIFO
    $isNext = $inquiry->category && 
              isset($nextInquiries[$inquiry->category->section]) && 
              $nextInquiries[$inquiry->category->section] == $inquiry->id;
@endphp
```

```javascript
// Show queue order warning
function showQueueOrderWarning(sectionName) {
    const message = sectionName 
        ? `You can only serve inquiries in ${sectionName} section queue order. The system enforces First-Come, First-Serve (FIFO) across all service types within this section, with priority alternation (NORMAL → PRIORITY).`
        : 'You can only serve inquiries in queue order. The system enforces First-Come, First-Serve (FIFO) with priority alternation.';
    showAlert(message, 'warning');
}
```

## Algorithm Details

### Priority Alternation Logic

The system determines the next inquiry using this decision tree:

```
1. If no priority inquiries exist:
   → Serve oldest NORMAL inquiry

2. If no normal inquiries exist:
   → Serve oldest PRIORITY inquiry

3. If starting fresh (no serving/completed today):
   → Serve oldest NORMAL inquiry (establish baseline fairness)

4. If last served was PRIORITY and NORMAL exists:
   → Serve oldest NORMAL inquiry (prevent priority monopolization)

5. If last served was NORMAL:
   → Serve oldest PRIORITY inquiry (priority gets preference)
```

### Database Query Structure

The section-wide queries use JOIN operations:

```sql
SELECT inquiries.* 
FROM inquiries
INNER JOIN categories ON inquiries.category_id = categories.id
WHERE categories.section = 'TEST_SECTION'
  AND inquiries.status = 'waiting'
  AND inquiries.date = '2026-03-08'
ORDER BY inquiries.created_at ASC
```

## Testing

### Automated Test

Run the CLI test script:
```bash
php test_section_fifo.php
```

This script will:
- Create test categories and inquiries
- Simulate the serving process with priority alternation
- Verify the FIFO order
- Test backend validation
- Provide detailed output

### Manual Testing

1. **Navigate to Admin Inquiries**: `/admin/inquiries`
2. **Observe Button States**: Only ONE "Serve" button should be enabled per section
3. **Test Serving Order**: 
   - Serve a NORMAL inquiry
   - Verify next enabled button is for PRIORITY (if available)
   - Serve PRIORITY
   - Verify next enabled button is for NORMAL
4. **Test Backend Validation**:
   - Try to click disabled buttons (should show warning)
   - Use browser dev tools to attempt bypassing UI
   - Backend should reject out-of-order requests

### Expected Results

**Scenario 1: Mixed Priorities Available**
```
Queue State (all in same section):
- ACS-001 (NORMAL, 08:00)   ← Served first
- ACS-002 (PRIORITY, 08:05) ← Enabled ✅ (next after NORMAL)
- ACS-003 (NORMAL, 08:10)   ← Disabled ⛔
- ACS-004 (PRIORITY, 08:15) ← Disabled ⛔

After serving ACS-002:
- ACS-001 (NORMAL, 08:00)   ← Completed
- ACS-002 (PRIORITY, 08:05) ← Being served
- ACS-003 (NORMAL, 08:10)   ← Enabled ✅ (next after PRIORITY)
- ACS-004 (PRIORITY, 08:15) ← Disabled ⛔
```

**Scenario 2: Only One Priority Type**
```
Queue State (all NORMAL):
- ACS-001 (NORMAL, 08:00) ← Enabled ✅ (pure FIFO)
- ACS-002 (NORMAL, 08:05) ← Disabled ⛔
- ACS-003 (NORMAL, 08:10) ← Disabled ⛔
```

**Scenario 3: Different Sections**
```
AGGREGATE AND CORRECTION SECTION:
- ACS-001 (NORMAL) ← Enabled ✅

ORIGINAL AND OTHER SURVEYS SECTION:
- OOSS-001 (NORMAL) ← Enabled ✅ (independent queue)
```

## Error Messages

### Backend Validation Error

When attempting to serve out-of-order:

```json
{
    "success": false,
    "message": "Cannot serve this inquiry out of order. In AGGREGATE AND CORRECTION section, the next in queue is ACS-002 (priority). First-Come, First-Serve across all service types in this section."
}
```

### Frontend Warning

When clicking disabled button:

```
You can only serve inquiries in AGGREGATE AND CORRECTION section queue order. 
The system enforces First-Come, First-Serve (FIFO) across all service types 
within this section, with priority alternation (NORMAL → PRIORITY).
```

## Benefits

### Improved Fairness
- Prevents "queue jumping" across service types
- Ensures chronological order is maintained
- Priority guests are served fairly without monopolizing service

### Better User Experience
- Clear visual indicators (only one enabled button)
- Informative error messages
- Predictable queue behavior

### System Integrity
- Backend validation prevents bypass attempts
- Section independence prevents cross-section interference
- Priority alternation algorithm is consistently enforced

## Backward Compatibility

- Existing inquiries maintain their data structure
- No database migrations required
- Categories still function normally
- Only the queue ordering logic changed

## Troubleshooting

### Issue: Multiple buttons enabled

**Solution:** Clear Laravel cache
```bash
php artisan cache:clear
php artisan view:clear
```

### Issue: Wrong inquiry shown as next

**Possible Causes:**
- Section field not populated in categories table
- Timezone issues affecting created_at ordering
- Cached view not reflecting changes

**Solution:**
1. Verify all categories have valid `section` values
2. Check database timezone settings
3. Clear all caches

### Issue: Priority alternation not working

**Possible Causes:**
- No completed inquiries to determine last served type
- All inquiries have same priority type
- Database query not returning correct results

**Solution:**
1. Complete at least one inquiry to establish baseline
2. Ensure mix of NORMAL and PRIORITY inquiries exist
3. Check Laravel logs for query errors

## Future Enhancements

Potential improvements:
- Configurable priority alternation patterns
- Section merging/splitting capabilities
- Advanced analytics on queue performance
- Real-time WebSocket updates for button states

## Support

For issues or questions regarding this implementation:
1. Check this documentation first
2. Review the test guide (`section_fifo_test_guide.html`)
3. Run automated tests to identify issues
4. Check Laravel logs for errors
5. Contact system administrator

---

**Implementation Date:** March 8, 2026  
**Version:** 1.0  
**Last Updated:** March 8, 2026
