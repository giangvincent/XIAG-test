import store from './index';

describe('Redux Store', () => {
    it('should have initial state', () => {
        const state = store.getState();
        expect(state.list.todos).toEqual([]);
    });

    it('should handle ADD_TODO', () => {
        store.dispatch({
            type: 'ADD_TODO',
            payload: { title: 'Test Todo', isDone: false }
        });

        const state = store.getState();
        expect(state.list.todos).toHaveLength(1);
        expect(state.list.todos[0].title).toEqual('Test Todo');
        expect(state.list.todos[0].id).toBeDefined();
    });

    it('should handle CHANGE_TODOS', () => {
        const newTodos = [{ id: '1', title: 'Updated', isDone: true }];
        store.dispatch({
            type: 'CHANGE_TODOS',
            payload: newTodos
        });

        const state = store.getState();
        expect(state.list.todos).toEqual(newTodos);
    });

    it('should handle REMOVE_TODO', () => {
        // Setup: Add two todos
        store.dispatch({ type: 'CHANGE_TODOS', payload: [] }); // Reset
        store.dispatch({ type: 'ADD_TODO', payload: { title: 'Todo 1', isDone: false } });
        store.dispatch({ type: 'ADD_TODO', payload: { title: 'Todo 2', isDone: false } });

        const stateBefore = store.getState();
        const idToRemove = stateBefore.list.todos[0].id;

        // Action: Remove the first todo by ID
        store.dispatch({ type: 'REMOVE_TODO', payload: idToRemove });

        const state = store.getState();
        expect(state.list.todos).toHaveLength(1);
        expect(state.list.todos[0].title).toEqual('Todo 2');
    });
});
