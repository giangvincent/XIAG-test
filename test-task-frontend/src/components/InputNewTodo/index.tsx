import React from "react";
import styles from "./InputNewTodo.module.css";

type InputNewTodoProps = {
  todoTitle: string;
  onChange: (todoTitle: string) => void;
  onSubmit: (todo: any) => void;
};

export const InputNewTodo = ({
  todoTitle,
  onChange,
  onSubmit,
}: InputNewTodoProps) => {
  // [Architecture] Refactor to Functional Component
  // Initialized state to avoid "cannot read property value of null" errors found during testing.

  // Logic: We rely on props for the input value (controlled component by parent MainApp),
  // but the original code used local state for submission.
  // The previous implementation was a mix of controlled (render uses prop) and uncontrolled (submit uses state).
  // Let's simplify: Use the prop `todoTitle` directly since `MainApp` updates it on change.

  // However, if we look at handleKeyDown in legacy code:
  // var val = this.state.value.trim();
  // It used a local state `value` which was synced in `componentDidUpdate`.
  // This is an anti-pattern (derived state synchronization).
  // Beause MainApp passes `todoTitle` and `onChange` updates `MainApp`'s state directly,
  // we should just use `todoTitle` prop!

  const handleChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    onChange(e.target.value);
  };

  const handleKeyDown = (event: React.KeyboardEvent) => {
    if (event.key !== "Enter") {
      return;
    }

    event.preventDefault();
    const val = todoTitle.trim();

    if (val) {
      onSubmit({
        title: val,
        isDone: false,
      });
      onChange("");
    }
  };

  return (
    <input
      className={styles["new-todo"]}
      type="text"
      value={todoTitle}
      onChange={handleChange}
      onKeyDown={handleKeyDown}
      placeholder="What needs to be done?"
    />
  );
};
