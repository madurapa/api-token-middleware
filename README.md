
# Basic API Token Middleware for Laravel

This middleware provides a simple, secure token-based authentication mechanism for Laravel, designed for internal machine-to-machine communication (e.g., internal apps accessing other internal APIs) without the complexity of OAuth.

> ⚠️ **Security Note**: Always send API tokens via `Authorization: Bearer` headers. Do not use query parameters or POST body fields as they are insecure and unsupported.

---

## Installation

Install the package via Composer:

```bash
composer require uogsoe/basic-api-token-middleware
```

Publish the database migration and model:

```bash
php artisan vendor:publish
```

Select:`UoGSoE\ApiTokenMiddleware\ApiTokenServiceProvider`

Run the migration to create the `api_tokens` table:

```bash
php artisan migrate
```

---

## Usage

### Creating a Token

Generate a token for a service (e.g., `testservice`):

```bash
php artisan apitoken:create testservice
```

> The token will only be displayed once. Store it securely.

---

### Protecting Routes

In `routes/api.php`, apply the middleware:

```php
use Illuminate\Support\Facades\Route;

Route::middleware('apitoken:testservice')->group(function () {
    Route::get('/hello', fn() => response()->json(['message' => 'Hello, World!']));
});
```

Multiple services:

```php
Route::middleware('apitoken:testservice,anotherservice')->group(function () {
    Route::get('/hello', fn() => response()->json(['message' => 'Hello, World!']));
});
```

---

### Authenticating Requests

Send requests using the Authorization header:

```bash
curl -H "Authorization: Bearer jT7ryt28gi3YCvgE4WvluO1uVcb0ndVx" https://my-project.test/api/hello
```

**Successful Response:**

```json
{"message": "Hello, World!"}
```

**Unauthorized Response:**

```json
{"message":"Unauthorized"}
```

**Laravel Example:**

```php
use Illuminate\Support\Facades\Http;
Http::withHeaders([
    'Authorization' => 'Bearer jT7ryt28gi3YCvgE4WvluO1uVcb0ndVx',
])->get('https://my-project.test/api/hello');
```

**AJAX Example:**

```javascript
fetch('https://my-project.test/api/hello', {
  method: 'GET',
  headers: {
    'Authorization': 'Bearer jT7ryt28gi3YCvgE4WvluO1uVcb0ndVx',
    'Accept': 'application/json'
  }
})
.then(response => response.json())
.then(data => console.log(data))
.catch(error => console.error('Error:', error));
```

> ❗ Avoid sending tokens via query strings or POST bodies - will result in a 401 Unauthorized response.

---

## Managing Tokens

- **List all tokens:**

```bash
php artisan apitoken:list
```

- **Regenerate a token:**

```bash
php artisan apitoken:regenerate testservice
```

- **Delete a token:**

```bash
php artisan apitoken:delete testservice
```

---

## Security Best Practices

- **Use HTTPS**: Encrypt all API traffic.
- **Secure Token Storage**: Use environment variables, secret vaults, or HTTP-only secure cookies. Avoid client-side exposure.
- **Token Expiry**: Use `apitoken:regenerate` periodically.
- **CORS Configuration** (in `config/cors.php`):

```php
'allowed_origins' => ['https://your-frontend.com'],
'supports_credentials' => true,
```

- **Rate Limiting**:

```php
Route::middleware('throttle:60,1')->get('/hello', fn() => response()->json(['message' => 'Hello, World!']));
```

- **XSS Protection**: Use Content Security Policy (CSP) and sanitize inputs.

---

## Upgrading from Previous Versions

If you were using `?api_token=` in URLs or POST bodies, **update clients** to use `Authorization: Bearer` headers immediately. These methods are no longer supported.

---

## Contributing

Contributions are welcome!  
Submit PRs at: [https://github.com/uogsoe/basic-api-token-middleware](https://github.com/uogsoe/basic-api-token-middleware)  
Ensure tests and security practices are followed.

---

## License

This project is licensed under the **MIT License**.
