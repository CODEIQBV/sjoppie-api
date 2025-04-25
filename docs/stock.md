# Stock Management

This document outlines the stock management functionality in the Sjoppie API. The system supports three types of stock tracking:
- Available Stock: The quantity available for sale
- On-Hand Stock: The physical quantity in inventory
- Reserved Stock: The quantity reserved for pending orders

## Stock Model

The stock system uses the following model structure:

```php
class ProductStock extends Model
{
    protected $fillable = [
        'available',  // Stock available for sale
        'on_hand',    // Physical stock in inventory
        'reserved',   // Stock reserved for orders
    ];
}
```

## Stock Actions

The system supports the following stock actions:

1. `initial` - Used when creating the first stock record for a product
2. `add` - Used when increasing stock quantities
3. `remove` - Used when decreasing stock quantities
4. `adjust` - Used when making manual adjustments to stock

## Endpoints

### Get All Stock Information
Retrieves all stock information for a product.

```http
GET /api/products/{product}/stock
```

**Response:**
```json
{
    "success": true,
    "status": 200,
    "data": {
        "available": 100,
        "on_hand": 120,
        "reserved": 20
    },
    "timestamp": "2024-04-25T12:00:00Z"
}
```

### Get Specific Stock Value
Retrieves a specific stock value for a product.

```http
GET /api/products/{product}/stock/available
GET /api/products/{product}/stock/on-hand
GET /api/products/{product}/stock/reserved
```

**Response:**
```json
{
    "success": true,
    "status": 200,
    "data": {
        "available": 100
    },
    "timestamp": "2024-04-25T12:00:00Z"
}
```

### Update Stock
Updates multiple stock values at once. This should be used sparingly as it doesn't log individual changes.

```http
PUT /api/products/{product}/stock
```

**Request Body:**
```json
{
    "available": 100,
    "on_hand": 120,
    "reserved": 20
}
```

**Response:**
```json
{
    "success": true,
    "status": 200,
    "data": {
        "available": 100,
        "on_hand": 120,
        "reserved": 20
    },
    "timestamp": "2024-04-25T12:00:00Z"
}
```

### Adjust Stock
Adjusts a specific stock value by adding or subtracting a quantity. This is the preferred method for stock changes as it includes logging.

```http
PUT /api/products/{product}/stock/available/adjust
PUT /api/products/{product}/stock/on-hand/adjust
PUT /api/products/{product}/stock/reserved/adjust
```

**Request Body:**
```json
{
    "quantity": 20,  // Can be positive or negative
    "reason": "Stock adjustment due to inventory check",
    "action": "adjust"  // Optional: "add", "remove", or "adjust"
}
```

**Parameters:**
- `quantity` (required): The amount to adjust (can be positive or negative)
- `reason` (optional): The reason for the adjustment
- `action` (optional): The type of adjustment ("add", "remove", or "adjust")

**Response:**
```json
{
    "success": true,
    "status": 200,
    "data": {
        "available": 120,
        "on_hand": 120,
        "reserved": 20
    },
    "timestamp": "2024-04-25T12:00:00Z"
}
```

## Stock Logging

All stock adjustments are automatically logged with the following information:
- Product ID
- User ID (if authenticated)
- Quantity change
- Previous quantity
- New quantity
- Action type
- Reason
- Timestamp
- Metadata (optional)

## Error Handling

The API returns appropriate error responses for various scenarios:

### Stock Not Found
```json
{
    "success": false,
    "message": "Stock not found",
    "status": 404
}
```

### Validation Error
```json
{
    "success": false,
    "errors": {
        "quantity": ["The quantity field is required."]
    },
    "status": 422
}
```

### Negative Stock Error
```json
{
    "success": false,
    "message": "Stock cannot be negative",
    "status": 400
}
```

## Best Practices

1. **Use Adjustments Instead of Direct Updates**
   - Always use the adjust endpoints for stock changes
   - This ensures proper logging and tracking
   - Direct updates should only be used for initial setup or bulk corrections

2. **Provide Clear Reasons**
   - Always include a reason for stock adjustments
   - This helps with auditing and tracking
   - Use consistent reason formats for better reporting

3. **Handle Stock Types Appropriately**
   - Use available stock for sales and customer-facing quantities
   - Use on-hand stock for physical inventory tracking
   - Use reserved stock for pending orders and holds
   - Keep available = on-hand - reserved for accurate tracking

4. **Monitor Stock Levels**
   - Regularly check stock levels
   - Set up alerts for low stock
   - Maintain accurate inventory counts
   - Perform regular physical inventory checks

5. **Use Appropriate Actions**
   - Use 'add' for new stock arrivals
   - Use 'remove' for stock reductions
   - Use 'adjust' for corrections and inventory counts
   - Use 'initial' only for first-time stock setup

## Example Workflows

### Adding New Stock
```http
PUT /api/products/{product}/stock/available/adjust
{
    "quantity": 50,
    "reason": "New shipment received from supplier #12345",
    "action": "add"
}
```

### Removing Stock
```http
PUT /api/products/{product}/stock/available/adjust
{
    "quantity": -10,
    "reason": "Damaged goods - Batch #2024-04-25",
    "action": "remove"
}
```

### Adjusting Physical Inventory
```http
PUT /api/products/{product}/stock/on-hand/adjust
{
    "quantity": -5,
    "reason": "Physical inventory count adjustment - Location A",
    "action": "adjust"
}
```

### Reserving Stock for Orders
```http
PUT /api/products/{product}/stock/reserved/adjust
{
    "quantity": 15,
    "reason": "Reserved for order #12345 - Customer: John Doe",
    "action": "add"
}
```

### Correcting Stock Discrepancy
```http
PUT /api/products/{product}/stock/available/adjust
{
    "quantity": -20,
    "reason": "System correction - Found extra stock in inventory",
    "action": "adjust"
}
```

## Common Scenarios

1. **Receiving New Stock**
   - Adjust both available and on-hand stock
   - Use 'add' action
   - Include supplier and batch information in reason

2. **Processing Sales**
   - Reduce available stock
   - Use 'remove' action
   - Include order number in reason

3. **Physical Inventory Count**
   - Adjust on-hand stock
   - Use 'adjust' action
   - Include location and count details in reason

4. **Order Reservations**
   - Increase reserved stock
   - Use 'add' action
   - Include order details in reason

5. **Stock Corrections**
   - Adjust appropriate stock type
   - Use 'adjust' action
   - Include detailed reason for correction 