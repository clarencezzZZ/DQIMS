# Category-Based Queueing System with Priority Management

## Overview
This system implements a Category-Based Queueing System for a government office with advanced priority management that ensures fair service between normal and priority guests.

## Problem Statement
- Previously had "normal", "priority", and "urgent" priority levels
- Need to remove "urgent" and only keep "normal" and "priority" 
- Need to implement a fairness algorithm to prevent priority guests from monopolizing service

## Solution Implemented

### 1. Database Changes
- Updated the `priority` enum in the `inquiries` table to only allow `['normal', 'priority']`
- Removed the `'urgent'` option from the enum
- Converted any existing 'urgent' records to 'priority' to maintain data integrity

### 2. Algorithm Logic
The system follows these rules:

#### Core Priority Algorithm
1. The system must always finish the currently serving person first
2. When starting fresh (no one currently serving):
   - Serve the oldest NORMAL inquiry first to establish baseline fairness
3. After the first call, the next number follows this logic:
   - If there is any PRIORITY waiting, call the oldest PRIORITY first
   - However, to avoid starving NORMAL guests, the system must not serve two PRIORITY guests in a row if a NORMAL guest is waiting
4. Fairness rules:
   - After serving one PRIORITY, the next call should be NORMAL (if any NORMAL exists)
   - If no NORMAL exists, continue serving PRIORITY
   - If no PRIORITY exists, serve NORMAL
   - If starting fresh, serve NORMAL first
5. Within the same type (Priority or Normal), use FIFO order (oldest first)
6. The logic applies separately per SECTION
   - Example: ACS queue should not affect OOSS queue

#### Example Scenarios
**Scenario 1 (Starting Fresh):**
- Waiting list in ACS: N1 (Normal), N2 (Normal), P1 (Priority), P2 (Priority)
- Correct serving order: N1 → P1 → N2 → P2

**Scenario 2 (Continue Serving):**
- Currently serving: N1 (Normal)
- Waiting: N2 (Normal), P1 (Priority)
- Next should be: P1
- After P1: N2

### 3. Pseudocode Implementation

```
FUNCTION getNextInquiryByPriority(categoryId)
    // Get all waiting inquiries in the category, ordered by creation time
    waitingInquiries = GET inquiries WHERE date=today AND category_id=categoryId AND status='waiting' ORDER BY created_at
    
    IF waitingInquiries.isEmpty() THEN
        RETURN NULL
    END IF
    
    // Get the currently serving inquiry (if any) and last completed inquiry
    currentlyServing = GET inquiry WHERE date=today AND category_id=categoryId AND status='serving' LIMIT 1
    lastServedInquiry = GET inquiry WHERE date=today AND category_id=categoryId AND status='completed' ORDER BY completed_at DESC LIMIT 1
    
    // Determine last served type
    IF currentlyServing IS NOT NULL THEN
        lastServedType = currentlyServing.priority
    ELSE
        lastServedType = lastServedInquiry ? lastServedInquiry.priority : NULL
    END IF
    
    // Separate priority and normal inquiries
    priorityInquiries = FILTER waitingInquiries WHERE priority == 'priority'
    normalInquiries = FILTER waitingInquiries WHERE priority == 'normal'
    
    // If there are no priority inquiries, return the oldest normal inquiry
    IF priorityInquiries.isEmpty() THEN
        RETURN normalInquiries.first()
    END IF
    
    // If there are no normal inquiries, return the oldest priority inquiry
    IF normalInquiries.isEmpty() THEN
        RETURN priorityInquiries.first()
    END IF
    
    // If starting fresh (no one currently serving and no last served), 
    // serve the oldest normal inquiry first to establish fairness
    IF lastServedType IS NULL THEN
        RETURN normalInquiries.first()
    END IF
    
    // If last served was priority and there are normal inquiries available,
    // return the oldest normal inquiry to avoid serving two priority in a row
    IF lastServedType == 'priority' THEN
        RETURN normalInquiries.first()
    END IF
    
    // Otherwise (last served was normal), return the oldest priority inquiry
    RETURN priorityInquiries.first()
END FUNCTION
```

### 4. Key Database Fields Used
- `inquiries.priority`: Enum field with values ['normal', 'priority']
- `inquiries.category_id`: Links inquiry to a specific section/category
- `inquiries.status`: Tracks status ('waiting', 'serving', 'completed', etc.)
- `inquiries.created_at`: Used for FIFO ordering within priority types
- `inquiries.completed_at`: Used to determine last served type

### 5. Files Modified
1. `database/migrations/2026_02_24_064306_update_priority_enum_in_inquiries_table.php` - Updated enum definition
2. `app/Http/Controllers/SectionController.php` - Implemented priority queuing algorithm
3. `app/Http/Controllers/AdminController.php` - Updated to handle conversion of urgent records
4. `resources/views/front-desk/create.blade.php` - Removed urgent option from UI

### 6. Implementation Details
- The algorithm maintains state by checking the last completed inquiry's priority type
- Each category/section operates independently with its own priority logic
- The system prevents priority guests from being served consecutively when normal guests are waiting
- FIFO ordering is maintained within each priority type
- All changes maintain backward compatibility while enforcing the new priority rules

## Benefits
- Eliminates the problematic "urgent" priority level
- Ensures fair service between normal and priority guests
- Maintains efficiency while preventing priority abuse
- Preserves FIFO within priority types
- Operates independently per section/category