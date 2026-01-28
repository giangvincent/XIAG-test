// import React, { useState, useEffect } from "react";
import { useState, useEffect } from "react";
import { Form, Button } from "react-bootstrap";
import { InputNewTodo } from "../InputNewTodo";
import UserSelect from "../UserSelect";
import { useDispatch, useSelector } from "react-redux";
import styles from "./MainApp.module.css";
import { Todo, User } from "../../types";

const MainApp = () => {
  // [Architecture] Modern React: Functional Components & Hooks
  // We replaced the Class component with a Functional component.
  // Benefit 1: Hooks (useState, useSelector) allows us to group related logic together, rather than splitting it by lifecycle methods.
  // Benefit 2: No more `this` binding confusion.
  // Benefit 3: Better minification and performance optimization by the engine.

  const [todoTitle, setTodoTitle] = useState("");
  const [users, setUsers] = useState<User[]>([]);
  const dispatch = useDispatch();

  // [Performance] Lifted State Up
  // We fetch users HERE (once), instead of in every UserSelect component (N times).
  // This solves the N+1 request problem.
  useEffect(() => {
    fetch("https://jsonplaceholder.typicode.com/users/")
      .then((resp) => resp.json())
      .then((data) => setUsers(data));
  }, []);

  // Select todos from Redux state
  const todos = useSelector(
    (state: { list: { todos: Todo[] } }) => state.list.todos,
  );

  const handleTodoTitle = (title: string) => {
    setTodoTitle(title);
  };

  const handleSubmitTodo = (todo: Todo) => {
    dispatch({ type: "ADD_TODO", payload: todo });
  };

  const handleChangeTodo = (idx: number) => {
    const changedTodos = todos.map((t, index) => {
      if (index === idx) {
        return { ...t, isDone: !t.isDone };
      }
      return t;
    });
    dispatch({ type: "CHANGE_TODOS", payload: changedTodos });
  };

  const handleRemoveTodo = (idx: number) => {
    dispatch({ type: "REMOVE_TODO", payload: idx });
  };

  // [Code Style] Derived State
  // We act directly on the `todos` array to check completion.
  // PREVIOUS ISSUE: The old code set a global variable `window.allTodosIsDone` inside the render loop.
  // That was a "Side Effect" which made the render impure and unpredictable.
  // FIXED: Pure calculation of derived state based on props/state.
  const allTodosIsDone = todos.length > 0 && todos.every((t) => t.isDone);

  return (
    <div>
      <Form.Check
        type="checkbox"
        label="all todos are done!"
        checked={allTodosIsDone}
        readOnly
      />
      <hr />
      <InputNewTodo
        todoTitle={todoTitle}
        onChange={handleTodoTitle}
        onSubmit={handleSubmitTodo}
      />
      {todos.map((t, idx) => (
        <div key={idx} className={styles.todo}>
          {t.title}
          <UserSelect user={t.user} idx={idx} users={users} />
          <Form.Check
            style={{ marginTop: -8, marginLeft: 5 }}
            type="checkbox"
            checked={t.isDone}
            onChange={() => handleChangeTodo(idx)}
          />
          <Button
            variant="danger"
            size="sm"
            onClick={() => handleRemoveTodo(idx)}
            style={{ marginLeft: "10px" }}>
            Del
          </Button>
        </div>
      ))}
    </div>
  );
};

export default MainApp;
