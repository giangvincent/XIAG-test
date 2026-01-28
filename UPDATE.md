# Deep Insight

This document details the critical architectural transformations applied to the codebase. The goal was not just to "fix bugs" but to elevate the system to modern engineering standards, focusing on **Security**, **Testability**, and **Predictability**.

---

## 🛡️ Backend: Security & Decoupling

### 1. The Shift to Dependency Injection (Inversion of Control)

- **Before:** `DataStorage` created its own `PDO` connection inside its constructor.
  - _Insight:_ This is "Tight Coupling". It makes `DataStorage` impossible to test without a real database connection. You cannot swap the database for a test double.
- **After:** `Application` creates the `PDO` instance and _injects_ it into `DataStorage`.
  - _Insight:_ This is "Inversion of Control". `DataStorage` no longer cares _how_ the database is connected, only that it _has_ a connection. This makes unit testing trivial (mocking PDO) and centralizes configuration in the Composition Root (`Application::run`).

### 2. Elimination of SQL Injection (Prepared Statements)

- **Before:** Variables were directly concatenated into SQL strings (`"SELECT ... id = " . $id`).
  - _Insight:_ This trusts user input implicitly. A malicious user could inject `1 OR 1=1` to dump the entire database.
- **After:** We switched to **Prepared Statements** (`:id` placeholders).
  - _Insight:_ The database engine compiles the SQL query structure _before_ data is inserted. Input is treated strictly as data, never as executable code. This is the only robust defense against SQLi.

### 3. Encapsulation of Global State

- **Before:** `ProjectController` accessed `$_REQUEST` directly.
  - _Insight:_ Superglobals are global state. Accessing them essentially "hides" inputs from the method signature, making the code unpredictable and hard to test.
- **After:** We use the `Request` object abstraction (`$request->request->all()`).
  - _Insight:_ The controller now depends on an explicit object interface. We can inject a fake `Request` object during tests to simulate any HTTP scenario without hacking global variables.

---

## ⚛️ Frontend: Purity & Immutability

### 1. Functional Components & Hooks vs. Classes

- **Before:** `MainApp` was a Class Component.
  - _Insight:_ Classes in JavaScript (and React) often lead to confusion with `this` binding and force logic to be split across lifecycle methods (`componentDidMount`, `componentDidUpdate`), making complex logic hard to follow.
- **After:** Refactored to a **Functional Component** using Hooks (`useState`, `useDispatch`, `useSelector`).
  - _Insight:_ Hooks allow us to group related logic together (colocation). The code is more declarative, easier to minify, and avoids the entire class overhead.

### 2. State Purity (The "Side-Effect" Problem)

- **Before:** `window.allTodosIsDone` was modified _inside_ the `render` method loops.
  - _Insight:_ The render phase should be "Pure" — calculating the Description of the UI based on state. Modifying a global variable is a "Side Effect". It makes rendering unpredictable (race conditions) and dirty.
- **After:** **Derived State**. `allTodosIsDone` is calculated on-the-fly (`todos.every(...)`) without modifying anything constant.
  - _Insight:_ This is the heart of declarative UI. We describe _what_ is true based on data, rather than manually flipping switches.

### 3. Redux Immutability

- **Before:** `state.todos.push(...)` mutated the state array in place.
  - _Insight:_ Redux and React rely on **Reference Equality** (`oldState === newState`) to decide if the UI should update. If you mutate the object in place, the reference doesn't change, so React thinks nothing happened and won't re-render.
  - _Insight 2:_ Mutation destroys "Time Travel" debugging capabilities.
- **After:** We return a **New Object** (`{ ...state, todos: [...] }`).
  - _Insight:_ By creating a new reference, we signal strictly to React that "Data has changed". This ensures predictable updates and respects the functional programming principles Redux is built on.

---

## 🚀 Recent Modernization & Fixes

### 1. Upgrade to React 18 (`createRoot`)

- **Before:** Used `ReactDOM.render()`, which is deprecated in React 18 and runs in "legacy mode" (behaving like React 17).
- **After:** Switched to `ReactDOM.createRoot()`.
  - _Insight:_ This unlocks React 18's **Concurrent Features** (like automatic batching). It required updating the entry point `src/index.tsx` to use the new `react-dom/client` API.

### 2. Backend Type Safety (Strict Typing)

- **Before:** `ProjectController` passed `$_GET` parameters directly to `DataStorage`, often resulting in `null` values crashing strict-typed methods (e.g., `TypeError: Argument #2 must be of type int, null given`).
- **After:** Inputs are strictly cast: `(int) $request->query->get('limit', 10)`.
  - _Insight:_ We explicitly define **Default Values** at the controller level. This makes the API robust against malformed or missing inputs and prevents the "garbage in, crash out" problem.

### 3. Modern Routing (PHP 8 Attributes)

- **Before:** Route configuration relied on Docblock Annotations `/** @Route(...) */`.
  - _Insight:_ Annotations are just comments; they require a separate parsing library (`doctrine/annotations`) to work at runtime, adding overhead and complexity.
- **After:** Migrated to native **PHP 8 Attributes** `#[Route(...)]`.
  - _Insight:_ Attributes are first-class citizens in PHP. They are performant, syntax-checked by the language itself, and don't require external hacks to function. This is the industry standard for modern PHP development.
