# Sjoppie API Development Workflow

This document outlines the standard workflow and conventions for developing new features in the Sjoppie API project.

## Directory Structure

```
app/
├── Http/
│   ├── Controllers/
│   │   └── Api/          # API controllers
│   ├── Middleware/       # Custom middleware
│   └── Requests/         # Form requests
├── Models/               # Eloquent models
├── Services/            # Business logic services
└── Resources/           # API resources
```

## Development Workflow

### 1. Database Changes

1. Create a new migration:
```bash
php artisan make:migration create_table_name_table
```

2. Always include:
- `timestamps()`
- `softDeletes()` if the model should support soft deletion
- Proper indexes
- Foreign key constraints where needed

### 2. Model Creation

1. Create model with migration:
```bash
php artisan make:model ModelName -m
```

2. Model requirements:
- Use `HasFactory` and `SoftDeletes` traits when needed
- Define `$fillable` properties
- Define `$casts` for JSON, boolean, etc.
- Add relationships
- Add accessors/mutators if needed

Example:
```php
class ModelName extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'field1',
        'field2',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'settings' => 'array',
    ];

    public function relatedModel()
    {
        return $this->belongsTo(RelatedModel::class);
    }
}
```

### 3. Service Layer

1. Create a service class in `app/Services/`
2. Services should:
- Handle business logic
- Use dependency injection
- Be testable
- Use caching when appropriate
- Follow single responsibility principle

Example:
```php
class ModelNameService
{
    public function __construct(
        protected RelatedService $relatedService
    ) {}

    public function getResource(): Resource
    {
        return Cache::remember('cache_key', 3600, function () {
            return $this->model->first();
        });
    }
}
```

### 4. API Controllers

1. Create controller in `app/Http/Controllers/Api/`
2. Controllers should:
- Use dependency injection
- Be thin (move logic to services)
- Return standardized responses
- Use form requests for validation
- Follow RESTful conventions

Example:
```php
class ModelNameController extends Controller
{
    public function __construct(
        protected ModelNameService $service
    ) {}

    public function index(): JsonResponse
    {
        return response()->json([
            'data' => $this->service->getAll(),
        ]);
    }
}
```

### 5. API Routes

1. Add routes in `routes/api.php`
2. Routes should:
- Be grouped under middleware
- Follow RESTful naming
- Use resource controllers when possible
- Be documented

Example:
```php
Route::middleware(['api.key', 'api.response'])->group(function () {
    Route::apiResource('resource', ResourceController::class);
});
```

### 6. Documentation

1. Create documentation in `docs/api/`
2. Documentation should include:
- Endpoint description
- Request/response examples
- All possible fields
- Field types and defaults
- Required/optional fields

Example file: `docs/api/resource.md`

## Key Principles

1. **Database**
- Use migrations for all schema changes
- Include proper indexes
- Use foreign keys for relationships
- Support soft deletes when appropriate

2. **Models**
- Use proper relationships
- Define fillable fields
- Use proper type casting
- Add accessors/mutators when needed

3. **Services**
- Handle business logic
- Use dependency injection
- Implement caching
- Be testable

4. **Controllers**
- Be thin
- Use dependency injection
- Return standardized responses
- Use form requests

5. **API Responses**
- Always use the `ApiResponseMiddleware`
- Follow the standard response format:
```json
{
    "success": true,
    "status": 200,
    "data": {},
    "message": "Optional message",
    "timestamp": "ISO8601"
}
```

6. **Caching**
- Cache expensive operations
- Use appropriate cache keys
- Clear cache on updates
- Use cache tags when possible

7. **Validation**
- Use form requests
- Validate all input
- Return clear error messages
- Use proper validation rules

8. **Error Handling**
- Use try-catch blocks
- Log errors
- Return proper error responses
- Use custom exceptions when needed

## Testing

1. Create tests in `tests/`
2. Test:
- Models
- Services
- Controllers
- API endpoints
- Edge cases
- Error scenarios

## Deployment

1. Always run tests before deployment
2. Check migrations
3. Clear cache if needed
4. Update documentation

## Common Tasks

### Creating a New Resource

1. Create migration
2. Create model
3. Create service
4. Create controller
5. Add routes
6. Create documentation
7. Add tests

### Updating Existing Resource

1. Create migration if needed
2. Update model
3. Update service
4. Update controller
5. Update documentation
6. Update tests

### Adding New Feature

1. Follow the same workflow
2. Consider existing patterns
3. Maintain consistency
4. Update documentation
5. Add tests

## Best Practices

1. **Code Style**
- Follow PSR-12
- Use proper type hints
- Add docblocks
- Keep methods small

2. **Security**
- Validate all input
- Use proper authentication
- Sanitize output
- Follow security best practices

3. **Performance**
- Use caching
- Optimize queries
- Use proper indexes
- Monitor performance

4. **Maintainability**
- Write clean code
- Add comments
- Follow SOLID principles
- Keep documentation updated 