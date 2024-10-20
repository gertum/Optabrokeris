import React, {createContext, useContext, useState} from 'react';
import {toast} from 'react-toastify';

const NotificationContext = createContext();

export const NotificationProvider = ({children}) => {
    const notifySuccess = (message) => {
        const id = toast.success(message);
    };

    const notifyError = (message) => {
        const id = toast.error(message);
    };

    return (
        <NotificationContext.Provider value={{
            notifySuccess,
            notifyError,
        }}>
            {children}
        </NotificationContext.Provider>
    );
};

export const useNotification = () => {
    return useContext(NotificationContext);
};
