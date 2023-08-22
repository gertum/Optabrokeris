import { useState, useEffect } from 'react';
import axios from 'axios';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head } from '@inertiajs/react';
import { Button, Layout, Steps, message } from 'antd';
import {
    FileUploadForm,
    LoadingForm,
    FinalForm,
} from '@/Components/NewJobSteps';
import { useTranslation } from 'react-i18next';

const { Content } = Layout;

export default function EditJob({ auth, jobId, jobType }) {
    const { t } = useTranslation();
    const [current, setCurrent] = useState(0);
    const [editedJob, setEditedJob] = useState({
        type: jobType,
        id: jobId,
    });
    const [token, setToken] = useState('');

    const onFormChange = (value) => {
        value ? setCurrent(current + 1) : console.log('You need to add file')
    };

    const onFinishLoading = () => {
        setCurrent(current + 1);
    };

    const onChange = value => {
        console.log('onChange:', value);
        setCurrent(value);
    };

    const ReusableButtons = () => {
        return (
            <div className="my-2">
                {current < 3 ? (
                    <Button htmlType="submit">Continue</Button>
                ) : (
                    <Button htmlType="submit">Download</Button>
                )}
            </div>
        );
    };

    const forms = [
        <FileUploadForm onFinish={onFormChange} newJob={editedJob} token={token}>
            <ReusableButtons />
        </FileUploadForm>,
        <LoadingForm onFinish={onFinishLoading} newJob={editedJob} />,
        <FinalForm data={editedJob}>
            <ReusableButtons />
        </FinalForm>,
    ];

    useEffect(() => {
        axios
            .get('/login')
            .then(response => {
                setToken(response.data);
            })
            .catch(error => {
                console.error('Error:', error);
            });
    }, []);

    return (
        <AuthenticatedLayout
            user={auth.user}
            header={
                <h2 className="font-semibold text-xl text-gray-800 leading-tight">
                    Edit Job
                </h2>
            }
        >
            <Head title="Edit Job" />
            <Content
                style={{
                    textAlign: 'center',
                    minHeight: 'calc(100vh - 128px)',
                    lineHeight: 4,
                }}
            >
                {editedJob?.id ? (
                    <div className="py-12">
                        <div className="max-w-7xl mx-auto sm:px-6 lg:px-8">
                            <div className="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                                <div className="p-6 text-gray-900">
                                    <Steps current={current} onChange={onChange}>
                                        <Steps.Step
                                            title={t('upload')}
                                            description={t('step.uploadFile')}
                                            disabled={current !== 2}
                                        />
                                        <Steps.Step
                                          title={t('step.execution')}
                                          description={t('step.solving')}
                                          disabled={current !== 3}
                                        />
                                        <Steps.Step
                                            title={t('step.success')}
                                            description={t('step.solutionReady')}
                                            disabled={current !== 4}
                                        />
                                    </Steps>
                                    {forms[current]}
                                </div>
                            </div>
                        </div>
                    </div>
                ) : (
                    <div>loading :D</div>
                )}
            </Content>
        </AuthenticatedLayout>
    );
}
