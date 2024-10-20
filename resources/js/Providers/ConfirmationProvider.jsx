import React, { createContext, useContext, useState } from 'react';
import { Modal } from 'antd';

const ConfirmationContext = createContext();

export const ConfirmationProvider = ({ children }) => {
    const requestConfirmation = (title, content) => {
        return new Promise((resolve) => {
            Modal.confirm({
                title,
                content,
                onOk() {
                    resolve(true);
                },
                onCancel() {
                    resolve(false);
                },
            });
        });
    };

    return (
        <ConfirmationContext.Provider value={{ requestConfirmation }}>
            {children}
        </ConfirmationContext.Provider>
    );
};

export const useConfirmation = () => {
    return useContext(ConfirmationContext);
};
