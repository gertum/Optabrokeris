import {Button, Divider, Form, Space, Upload} from 'antd';
import {useTranslation} from 'react-i18next';
import {useState} from "react";

export const FileUploadForm = ({newJob, onFinish, onUploadChange, children, token, disabled}) => {
    const {t} = useTranslation();
    const {file} = useState(null);

    return <>
        <Divider orientation="left">Upload a working file</Divider>
        <Form onFinish={() => onFinish()} className="mt-4">
            <p>Need a sample file? <a href="/download/school-example" target={'_blank'}>Click here</a></p>
            <Upload.Dragger
                action={`/api/job/${newJob.id}/upload?_token=${token}`}
                maxCount={1}
                listType="picture"
                accept=".xlsx"
                onChange={(file) => {
                    onUploadChange(file)
                }}
            >
                {t('step.fileUploadForm.dragFiles')}
                <br />
                <Space>
                    <Button>{t('upload')}</Button>
                </Space>
            </Upload.Dragger>
            {children}
        </Form>
    </>;
};
