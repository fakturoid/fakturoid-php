# Upgrade Guide

## Upgrading from 4.x to 5.0

### Breaking Changes

#### RequestException::getResponse() return type change

**Changed:** `RequestException::getResponse()` now returns `Fakturoid\Response` instead of `Psr\Http\Message\ResponseInterface`.

**Reason:** This change provides easier access to rate limiting information and other Fakturoid-specific response features.

**Impact:** If you were catching exceptions and using the response object, you may need to update your code.

#### Migration Examples:

**Before (v4.x):**
```php
use Fakturoid\Exception\ClientErrorException;
use Psr\Http\Message\ResponseInterface;

try {
    $response = $fManager->getInvoicesProvider()->create($data);
} catch (ClientErrorException $e) {
    /** @var ResponseInterface $response */
    $response = $e->getResponse();
    $statusCode = $response->getStatusCode();
    $reasonPhrase = $response->getReasonPhrase();
    $body = $response->getBody()->getContents();
}
```

**After (v5.0):**
```php
use Fakturoid\Exception\ClientErrorException;
use Fakturoid\Response;

try {
    $response = $fManager->getInvoicesProvider()->create($data);
} catch (ClientErrorException $e) {
    /** @var Response $response */
    $response = $e->getResponse();
    $statusCode = $response->getStatusCode();
    
    // Access to PSR ResponseInterface if needed
    $reasonPhrase = $response->getOriginalResponse()->getReasonPhrase();
    
    // Body is now easier to access
    $body = $response->getBody(); // Returns decoded JSON or string
    
    // NEW: Access to rate limiting information
    if ($e->isRateLimitExceeded()) {
        $resetTime = $response->getRateLimitReset();
        $remaining = $response->getRateLimitRemaining();
    }
}
```

#### Key Changes:

1. **Type hint change**: If you were type-hinting `ResponseInterface`, change it to `Response`
2. **Access to PSR response**: Use `$response->getOriginalResponse()` if you need the original PSR ResponseInterface
3. **Body access**: `$response->getBody()` now returns decoded JSON (array/object) or string, instead of StreamInterface
4. **Headers access**: Headers are now accessible via `$response->getHeader('Header-Name')`

## Upgrading from 3.x to 4.0

See [CHANGELOG.md](CHANGELOG.md) for details.

## Upgrading from 2.x to 3.0

See [CHANGELOG.md](CHANGELOG.md) for details.
