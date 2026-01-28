import { configureStore } from '@reduxjs/toolkit'
import { Todo, TodoState } from '../types';

export default configureStore({
    reducer: {
        list: (state: TodoState = {todos: []}, action: any) => {
            switch (action.type) {
                case 'ADD_TODO': {
                    // [Architecture] Immutability Pattern
                    // Redux rely on reference equality checks (oldState === newState) to determine if the UI needs to update.
                    // If we mutate `state` directly (e.g. state.push), the reference is the same, so React won't re-render.
                    // FIX: We return a NEW object with a NEW array, copying the old values.
                    return {
                        ...state,
                        todos: [...state.todos, action.payload]
                    };
                }
                case 'REMOVE_TODO': {
                    return {
                        ...state,
                        todos: state.todos.filter((t: Todo, index: number) => index !== action.payload),
                    };
                }
                case 'CHANGE_TODOS': {
                    return {
                        ...state,
                        todos: action.payload,
                    };
                }
                default:
                    return state;
            }
        }
    }
})
