import React from 'react';
import { Form, Input } from 'antd';
import { useTranslation } from 'react-i18next';

export const NamingForm = ({ onFinish, children }) => {
  const { t } = useTranslation();

  const enterNameLabel = t('step.namingForm.enterName');
  const pleaseEnterNameMessage = t('step.namingForm.pleaseEnterJobName');

  return (
    <div className="my-2">
      <Form onFinish={onFinish}>
        <Form.Item
          label={enterNameLabel} // Use the translated label
          name="name"
          rules={[
            {
              required: true,
              message: pleaseEnterNameMessage, // Use the translated message
            },
          ]}
        >
          <Input
            size="small"
            placeholder={enterNameLabel} // Use the translated label as placeholder
          />
        </Form.Item>
        {children}
      </Form>
    </div>
  );
};
