# Current solution review

## 1. Security / Data Privacy

Issues related to API / application / database security

### 1.1 Unprotected endpoints [severity:critical]
No authorization requested

### 1.2 Exception handling  [severity:critical]
Displays stack trace - no JSON response with related error returned

### 1.3 Exposing model data on the view layer [severity:critical]
Expose database interna id, sensitive information

### 1.4 The service `bookstore_api` runs as root [severity:critical] 

### `X-Powered-By` header exposure [severity:low] 

---

## 2. Performance

Issues related to API / database / infrastructure performance / monitoring / scalability etc ..

### 2.1 No healthcheck endpoint exists on api service [severity:critical]

Important for cloud native deployments

### 2.2 Missing logs [severity:critical]

### 2.3 No usage of correlation id in logs [severity:high]
Requires 2.2

### 2.4 No paged results on list objects endpoints [severity:critical]

### 2.5 No cache HTTP response headers [severity:medium]
ETag, etc ..

### 2.6 No distributed caching layer [severity:medium]
Avoid DB load if possible

---

## 3. Functionality

### 3.1 No unique constraint on table `authors` and column `name` [severity:low]

### 3.2 No unique constraint on table `books` and column `isbn` [severity:critical]

### 3.3 HTTP POST endpoints (new resource) should be idempotent [severity:critical]

---

## 4. Code Architecture / Testing

### 4.1 No separation of concerns [severity:critical]

### 4.2 Business logic on controllers [severity:critical]

### 4.3 Missing edge test cases [severity:high]
Should fix 1.2 first 
Returns correct HTTP code etc ..

### 4.4 Composer scripts using npm / npx but no installation defined on the Dockerfile [severity:low]

A separate Dockerfile for development

### 4.5 An OpenAPI documentation [severity:low]

### 4.6 No CI setup [severity:critical]


