import React from "react";
import { render, screen } from "@testing-library/react";
import { Provider } from "react-redux";
import store from "../../store";
import UserSelect from "./index";
import "@testing-library/jest-dom"; // For toBeInTheDocument

describe("UserSelect", () => {
  beforeEach(() => {
    // Mock fetch
    global.fetch = jest.fn(() =>
      Promise.resolve({
        json: () =>
          Promise.resolve([
            { id: 1, name: "User 1" },
            { id: 2, name: "User 2" },
          ]),
      }),
    ) as jest.Mock;
  });

  afterEach(() => {
    jest.restoreAllMocks();
  });

  it("renders select element", async () => {
    render(
      <Provider store={store}>
        <UserSelect idx={0} user={1} />
      </Provider>,
    );
    // Wait for fetch to populate options? Actually select element is always there.
    expect(screen.getByRole("combobox")).toBeInTheDocument();
  });

  it("displays default option", async () => {
    render(
      <Provider store={store}>
        <UserSelect idx={0} user={1} />
      </Provider>,
    );
    // Use findByText to wait for async content appearance
    expect(await screen.findByText("User 1")).toBeInTheDocument();
  });
});
