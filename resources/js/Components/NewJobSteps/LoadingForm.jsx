import { Form } from 'antd';
import { useTranslation } from 'react-i18next';

export const LoadingForm = ({ children }) => {
  const { t } = useTranslation();

  return (
    <div className="my-2">
      <Form>{children}</Form>
    </div>
  );
};
