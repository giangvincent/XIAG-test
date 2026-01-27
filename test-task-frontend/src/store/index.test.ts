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
    });

    it('should handle CHANGE_TODOS', () => {
        const newTodos = [{ title: 'Updated', isDone: true }];
        store.dispatch({
            type: 'CHANGE_TODOS',
            payload: newTodos
        });

        const state = store.getState();
        expect(state.list.todos).toEqual(newTodos);
    });
});
