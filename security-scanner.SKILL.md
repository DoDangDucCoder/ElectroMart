---
name: vibe-security-scanner
description: 'Scan Vibe/Laravel code for 20 common security vulnerabilities including hardcoded secrets, SQL injection, XSS, IDOR, and access control issues'
---

# Vibe Security Scanner Skill

This skill scans Laravel/Vibe codebases for critical security vulnerabilities commonly introduced by AI-assisted coding.

## Vulnerabilities Detected

1. **Hardcoded Secrets** - API keys, database passwords, tokens embedded in code
2. **SQL Injection** - String concatenation in queries instead of parameterized statements
3. **XSS (Cross-Site Scripting)** - Unescaped user input rendered directly in HTML/Blade templates
4. **IDOR (Insecure Direct Object Reference)** - Direct ID manipulation without authorization checks
5. **Slopsquatting** - Typosquatted or non-existent library dependencies that could contain malware
6. **Brute Force** - Missing rate limiting on authentication endpoints (login, OTP, password reset)
7. **Mass Assignment** - Unguarded model properties allowing clients to set restricted fields
8. **Insecure Deserialization** - Unsafe use of pickle, YAML, or unserialize() on untrusted data
9. **SSRF (Server-Side Request Forgery)** - Server making requests to user-supplied URLs without validation
10. **Path Traversal** - File operations allowing `../` sequences to access restricted directories
11. **CSRF (Cross-Site Request Forgery)** - Missing CSRF token validation on state-changing endpoints
12. **Broken Access Control** - Missing authorization checks at backend; relying only on frontend hiding
13. **Weak Password Hashing** - Plaintext, MD5, or SHA1 password storage instead of bcrypt/argon2
14. **JWT None Algorithm** - Allowing JWT with `algorithm: "none"` or weak secrets
15. **CORS Misconfiguration** - Overly permissive Access-Control-Allow-Origin headers
16. **Unrestricted File Upload** - Missing file type/size validation on upload endpoints
17. **Verbose Error Messages** - Stack traces in production responses revealing system details
18. **Missing Rate Limit** - No request throttling on resource-intensive or repeated endpoints
19. **Race Condition** - Concurrent operations on shared resources without locking (payments, inventory)
20. **Outdated Dependency** - Using packages with known CVEs; failing to update regularly

## How to Use

Ask the AI agent to:
- **Scan the entire codebase**: "Use the vibe-security-scanner skill to scan Laravel11_Shop_Website_Full for all 20 vulnerabilities"
- **Scan specific files**: "Scan app/Http/Controllers/* for security issues"
- **Check a vulnerability type**: "Check for SQL injection and XSS in Laravel11_Shop_Website_Full"

## Output Format

The scanner will:
1. **Identify** each vulnerability found with file path and line numbers
2. **Explain** why it's a security risk
3. **Show** the problematic code snippet
4. **Suggest** the fix with secure code example
5. **Categorize** by severity (Critical, High, Medium, Low)

## Example Findings

```
[CRITICAL] Hardcoded Secret in config/services.php:12
  STRIPE_SECRET_KEY = "sk_live_abcd1234..."
  → Move to .env file, never commit secrets to git

[HIGH] SQL Injection in app/Http/Controllers/ProductController.php:45
  $products = DB::raw("SELECT * FROM products WHERE id = " . $id);
  → Use parameterized query: DB::table('products')->where('id', $id)->get()

[MEDIUM] Missing Rate Limit in app/Http/Controllers/AuthController.php:10
  Route::post('/login', [AuthController::class, 'login']);
  → Add throttle middleware: Route::post('/login', [...])→middleware('throttle:5,1')
```

## Scanning Patterns

### For Each Vulnerability Type:

**Hardcoded Secrets**: Search for patterns like API keys, password assignments, secret tokens in .php, .js, .env.example files

**SQL Injection**: Find DB::raw(), DB::statement(), direct string concatenation in queries, escaped only with quotes not parameters

**XSS**: Locate {!! !!} in Blade templates with unescaped variables, echo statements without e() helper, dangerouslySetInnerHTML equivalents

**IDOR**: Check routes/controllers that take IDs from URL/request without verifying user owns the resource

**Slopsquatting**: Validate composer.json dependencies against packagist, check for typos in package names

**Brute Force**: Find login, OTP, password reset endpoints without rate limiting or throttle middleware

**Mass Assignment**: Check Model classes for unguarded $fillable properties or use of fill()/update() with request()->all()

**Insecure Deserialization**: Search for unserialize(), yaml parsing, pickle equivalents on untrusted input

**SSRF**: Locate HTTP client calls with user-supplied URLs without validation (file_get_contents, cURL, guzzle with user URL)

**Path Traversal**: Find file operations with user input in paths (Storage::get($userInput), include $path, fopen)

**CSRF**: Check for missing @csrf tokens in forms, POST/PUT/DELETE endpoints without CSRF middleware

**Broken Access Control**: Find endpoints missing authorization checks (no Gate::authorize, no policy checks, no user_id verification)

**Weak Password Hashing**: Search for MD5, SHA1, plain text password storage instead of Hash::make()

**JWT None Algorithm**: Check JWT configuration, token generation for algorithm field, secret strength

**CORS**: Review middleware/headers for Access-Control-Allow-Origin: * or overly permissive values

**Unrestricted File Upload**: Find file upload handlers lacking validation on extension, size, MIME type

**Verbose Error Messages**: Check exception handlers, error responses for stack traces, file paths, database structure in production

**Missing Rate Limit**: Find endpoints without throttle middleware on sensitive operations (API, payments, reports)

**Race Condition**: Locate concurrent operations on shared resources without database locks or transactions

**Outdated Dependency**: Run composer outdated, check for packages with known CVEs

## Notes

- Scans Laravel/Vibe code patterns and configurations
- Best for catching AI-introduced vulnerabilities early in development
- Run before deployment and code review
- False positives may occur—manual verification recommended for critical findings
- Complements static analysis tools like PHPStan, Larastan, and SonarQube
