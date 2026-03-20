# Architecture: LexikJWTAuthenticationBundle

## Purpose

Symfony bundle that implements JWT (JSON Web Token) authentication. It handles token creation, extraction from requests, decoding, and integration with Symfony Security's authenticator system.

## Directory Structure

```
Security/
  Authenticator/          Symfony Security HTTP authenticator + post-auth token
Exception/                Domain exceptions (expired, invalid, missing token)
Event/                    JWT lifecycle events (not found, invalid, authenticated, etc.)
Response/                 Authentication failure/success response wrappers
Services/                 JWTTokenManager — signs and parses tokens
TokenExtractor/           Extracts raw JWT string from Header, Cookie, or query param
Resources/doc/            RST documentation for configuration and usage
Tests/
  Functional/             Full Symfony kernel tests with real HTTP requests
  Security/Authenticator/ Unit tests for the authenticator
```

## Key Design Decisions

- **Event-driven**: Every authentication step dispatches an event so applications can customise responses, add claims, or log failures without overriding the bundle.
- **Multiple extractors**: A chain extractor tries each registered extractor (header, cookie, query) in order, making it easy to add custom extraction strategies.
- **Passport / SelfValidatingPassport**: Relies on Symfony 6+ `AbstractAuthenticator` with `SelfValidatingPassport`, delegating user loading to the user provider.
- **Pluggable encoder**: Supports both `lcobucci/jwt` and `web-token/jwt-framework` as the underlying JWT library, selected via configuration.

## Extension Points

- Subscribe to `JWT_CREATED`, `JWT_DECODED`, `JWT_AUTHENTICATED`, `JWT_NOT_FOUND`, `JWT_INVALID`, `JWT_EXPIRED` events.
- Implement `JWTUserProviderInterface` to load users directly from the JWT payload.
- Register custom `TokenExtractorInterface` services tagged `lexik_jwt_authentication.token_extractor`.

## Dependency Flow

```
HTTP Request
  -> JWTAuthenticator::supports()
    -> TokenExtractor chain
  -> JWTAuthenticator::authenticate()
    -> JWTTokenManagerInterface::parse()
    -> EventDispatcher (JWT_DECODED)
    -> UserProvider::loadUserByIdentifier()
    -> SelfValidatingPassport
  -> JWTAuthenticator::createToken()
    -> JWTPostAuthenticationToken
```
