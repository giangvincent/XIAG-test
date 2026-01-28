export interface User {
    id: number;
    name: string;
}

export interface Todo {
    title: string;
    user?: number;
    isDone: boolean;
}

export interface TodoState {
    todos: Todo[];
}
