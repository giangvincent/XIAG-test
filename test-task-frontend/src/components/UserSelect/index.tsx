import React from "react";
import { useDispatch, useSelector } from "react-redux";
import styles from "./UserSelect.module.css";
import { User, Todo } from "../../types";

type UserSelectProps = {
  user?: number;
  idx: number;
  users: User[];
};

function UserSelect({ user, idx, users }: UserSelectProps) {
  const dispatch = useDispatch();
  const todos = useSelector(
    (state: { list: { todos: Todo[] } }) => state.list.todos,
  );

  const handleChange = (e: React.ChangeEvent<HTMLSelectElement>) => {
    const changedTodos = todos.map((t, index) => {
      const res = { ...t };
      if (index === idx) {
        res.user = parseInt(e.target.value, 10);
      }
      return res;
    });
    dispatch({ type: "CHANGE_TODO", payload: changedTodos });
  };

  return (
    <select
      name="user"
      className={styles.user}
      onChange={handleChange}
      value={user || ""}>
      <option value="">Select User</option>
      {users.map((u) => (
        <option key={u.id} value={u.id}>
          {u.name}
        </option>
      ))}
    </select>
  );
}

export default UserSelect;
