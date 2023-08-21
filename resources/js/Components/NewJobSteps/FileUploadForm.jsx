import { Button, Form, Upload } from 'antd';
import { useTranslation } from 'react-i18next';

export const FileUploadForm = ({ newJob, onFinish, children, token }) => {
  const { t } = useTranslation();

  return (
    <div className="my-2">
      <Form onFinish={() => onFinish({ fileData: data })} className="mt-4">
        <Button
          className="my-2"
          onClick={() => console.log('Downloading solver data example...')}
        >
          {t('step.fileUploadForm.downloadExample')}
        </Button>
        <Upload.Dragger
          action={`/api/job/${newJob.id}/upload?_token=${token}`}
          maxCount={1}
          listType="picture"
          accept=".xlsx"
        >
          {t('step.fileUploadForm.dragFiles')}
          <br />
          <Button>{t('upload')}</Button>
        </Upload.Dragger>
        {children}
      </Form>
    </div>
  );
};
