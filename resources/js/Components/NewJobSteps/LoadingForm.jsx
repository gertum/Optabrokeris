import { Button, Form } from 'antd';
import { useTranslation } from 'react-i18next';

export const LoadingForm = ({ onFinish }) => {
  const { t } = useTranslation();

  return (
    <div className="my-2">
      <Form>
        {/*<Spin tip="Executing...">*/}
        {/*  <div*/}
        {/*    style={{*/}
        {/*      width: '100%',*/}
        {/*      height: '30vh',*/}
        {/*      display: 'flSolvingex',*/}
        {/*      justifyContent: 'center',*/}
        {/*      alignItems: 'center',*/}
        {/*    }}*/}
        {/*  >*/}
        {/*    <span></span>*/}
        {/*  </div>*/}
        {/*</Spin>*/}
        <Button onClick={onFinish}>{t('step.solve')}</Button>
      </Form>
    </div>
  );
};
