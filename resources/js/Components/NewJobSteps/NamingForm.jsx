import React from 'react';
import {Divider, Form, Input} from 'antd';
import {useTranslation} from 'react-i18next';

export const NamingForm = ({onChange, defaultValue, children}) => {
    const {t} = useTranslation();

    const enterNameLabel = t('step.namingForm.enterName');
    const pleaseEnterNameMessage = t('step.namingForm.pleaseEnterJobName');

    const handleValuesChange = (changedValues) => {
        onChange(changedValues);
    };


    return <>
        <Divider orientation="left">Name of a job</Divider>
        <Form onValuesChange={handleValuesChange} initialValues={{name: defaultValue}}>
            <Form.Item
                label={false} // Use the translated label
                name="name"
                rules={[
                    {
                        required: true,
                        message: pleaseEnterNameMessage, // Use the translated message
                    },
                ]}
            >
                <Input
                    size="large"
                    placeholder={enterNameLabel} // Use the translated label as placeholder
                />
            </Form.Item>
            {children}
        </Form>
        <Divider />
    </>;
};
