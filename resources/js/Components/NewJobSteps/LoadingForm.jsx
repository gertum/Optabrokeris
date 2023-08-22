import { useEffect } from 'react';
import { Form, Spin } from 'antd';

export const LoadingForm = ({ onFinish }) => {
  useEffect(() => {
    onFinish();
  }, []);

  return (
    <div className="my-2">
      <Form>
        <Spin tip="Executing...">
          <div
            style={{
              width: '100%',
              height: '30vh',
              display: 'flex',
              justifyContent: 'center',
              alignItems: 'center',
            }}
          >
            <span></span>
          </div>
        </Spin>
      </Form>
    </div>
  );
};
