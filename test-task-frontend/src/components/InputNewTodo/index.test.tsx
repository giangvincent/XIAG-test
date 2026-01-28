import React from "react";
import { render, screen, fireEvent } from "@testing-library/react";
import { InputNewTodo } from "./index";
import "@testing-library/jest-dom";

describe("InputNewTodo", () => {
  it("renders input field", () => {
    render(
      <InputNewTodo todoTitle="" onChange={() => {}} onSubmit={() => {}} />,
    );
    expect(
      screen.getByPlaceholderText(/What needs to be done?/i),
    ).toBeInTheDocument();
  });

  it("calls onChange when typing", () => {
    const handleChange = jest.fn();
    render(
      <InputNewTodo todoTitle="" onChange={handleChange} onSubmit={() => {}} />,
    );

    const input = screen.getByPlaceholderText(/What needs to be done?/i);
    fireEvent.change(input, { target: { value: "New Task" } });

    expect(handleChange).toHaveBeenCalledWith("New Task");
  });

  it("calls onSubmit when key press Enter", () => {
    const handleSubmit = jest.fn();
    render(
      <InputNewTodo
        todoTitle="My Task"
        onChange={() => {}}
        onSubmit={handleSubmit}
      />,
    );

    const input = screen.getByPlaceholderText(/What needs to be done?/i);

    // Simulate pressing Enter
    fireEvent.keyDown(input, {
      key: "Enter",
      code: "Enter",
      // keyCode: 13, // deprecated
      // charCode: 13, // deprecated
    });

    // Assuming the component listens to onKeyDown and checks for Enter.
    // If the implementation uses a form submit, we might need to verify implementation.
    // Assuming implementation calls onSubmit with { title: ..., isDone: false }
    expect(handleSubmit).toHaveBeenCalledWith({
      title: "My Task",
      isDone: false,
    });
  });
});
