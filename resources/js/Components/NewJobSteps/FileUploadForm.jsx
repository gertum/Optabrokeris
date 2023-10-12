import {Button, Form, Space, Upload} from 'antd';
import { useTranslation } from 'react-i18next';
import {useState} from "react";

export const FileUploadForm = ({ newJob, onFinish, onUploadChange, children, token }) => {
  const { t } = useTranslation();
  const { file } = useState(null);

  return (
    <div className="my-2">
      <Form onFinish={() => onFinish()} className="mt-4">
        <p>Need a sample file? <a href="/download/school-example" target={'_blank'}>Click here</a></p>
        <Upload.Dragger
          action={`/api/job/${newJob.id}/upload?_token=${token}`}
          maxCount={1}
          listType="picture"
          accept=".xlsx"
          onChange={(file) => { onUploadChange(file) }}
        >
          {t('step.fileUploadForm.dragFiles')}
          <br />
          <Space>
            <Button>{t('upload')}</Button>
          </Space>
        </Upload.Dragger>
        {children}
      </Form>
    </div>
  );
};
