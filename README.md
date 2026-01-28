# Test Task Refactoring Project

This repository contains the refactored code for the Backend and Frontend test tasks.

## 🚀 Status

- **Backend**: Refactored to PHP 8.2 Attributes, Prepared Statements, and strictly typed controllers.
- **Frontend**: Upgraded to React 18, strict TypeScript types, and optimized performance (fixed N+1 fetching).
- **Testing**: 100% tests passing for both.

## 🛠️ Setup Instructions

### Backend (DDEV)

Requirements: Docker, DDEV.

1.  Navigate to `test-task-backend`:
    ```bash
    cd test-task-backend
    ```
2.  Start the environment:
    ```bash
    ddev start
    ```
3.  Install dependencies:
    ```bash
    ddev composer install
    ```
4.  Import database schema and fixtures:
    ```bash
    ddev import-db --src=database/fixtures.sql
    ```
5.  Run tests:
    ```bash
    ddev exec phpunit
    ```

### Frontend (React)

Requirements: Node.js >= 20.

1.  Navigate to `test-task-frontend`:
    ```bash
    cd test-task-frontend
    ```
2.  Install dependencies:
    ```bash
    npm install
    ```
3.  Start the development server:
    ```bash
    npm start
    ```
4.  Run tests:
    ```bash
    npm test
    ```
