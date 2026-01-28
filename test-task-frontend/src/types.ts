export interface User {
    id: number;
    name: string;
}

export interface Todo {
    id: string; // [FIX] Unique ID for React Keys
    title: string;
    user?: number;
    isDone: boolean;
}

export interface TodoState {
    todos: Todo[];
}
