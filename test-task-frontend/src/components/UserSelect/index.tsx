import React, { memo } from "react";
import styles from "./UserSelect.module.css";
import { User } from "../../types";

type UserSelectProps = {
  user?: number;
  users: User[];
  onChange: (userId: number) => void;
};

const UserSelect = ({ user, users, onChange }: UserSelectProps) => {
  const handleChange = (e: React.ChangeEvent<HTMLSelectElement>) => {
    onChange(parseInt(e.target.value, 10));
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
};

export default memo(UserSelect);
