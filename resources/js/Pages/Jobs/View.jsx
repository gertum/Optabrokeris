import React, { useEffect, useState } from 'react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head } from '@inertiajs/react';
import { Button, Col, Divider, Form, Layout, Row, Space, Upload, Statistic, Alert } from 'antd';
import { useTranslation } from 'react-i18next';
import axios from 'axios';
import { useNotification } from '@/Providers/NotificationProvider.jsx';
import { useConfirmation } from '@/Providers/ConfirmationProvider.jsx';
import {
    DownloadOutlined,
    DislikeOutlined,
    LikeOutlined,
    LoadingOutlined,
    PlayCircleOutlined
} from '@ant-design/icons';

const { Content } = Layout;
const { Title } = Head;

export default function JobView({ auth, job: initialJob }) {
    const { t } = useTranslation();
    const { notifySuccess, notifyError } = useNotification();
    const [job, setJob] = useState(initialJob);
    const [token, setToken] = useState('');
    const [countdown, setCountdown] = useState(0);
    const waitTime = 30;

    const fetchToken = async () => {
        try {
            const tokenResponse = await axios.get('/login');
            setToken(tokenResponse.data);
        } catch (error) {
            notifyError(`Login error: ${error.message}`);
        }
    };

    const handleSolve = async () => {
        await axios.post(`/api/job/${job.id}/solve?_token=${token}`)
            .catch((error) => {
                console.log('error response:', error);

                let errorMessage = error.response.data;
                if (errorMessage === undefined || typeof errorMessage !== 'string') {
                    errorMessage = error.message;
                }
                notifyError(errorMessage);
            })
            .then((response) => {
                if (response !== undefined) {
                    notifySuccess('Solving started');
                    if (response.data.id !== undefined) {
                        setJob(response.data);
                        setCountdown(waitTime); // Start countdown
                    }
                }
            });
    };

    const handleStop = async () => {
        await axios.post(`/api/job/${job.id}/stop?_token=${token}`)
            .catch((error) => {
                console.log('error response:', error);

                let errorMessage = error.response.data;
                if (errorMessage === undefined || typeof errorMessage !== 'string') {
                    errorMessage = error.message;
                }
                notifyError(errorMessage);
            })
            .then((response) => {
                if (response !== undefined) {
                    notifySuccess('Solving stop signal sent');
                    reloadJobContent();
                }
            });
    };

    const reloadJobContent = async () => {
        await axios.request({
            method: 'GET',
            url: `/api/job/${job.id}?_token=${token}`,
        })
            .catch((error) => {
                console.log('error response:', error);

                let errorMessage = error.response.data;
                if (errorMessage === undefined || typeof errorMessage !== 'string') {
                    errorMessage = error.message;
                }
                notifyError(errorMessage);
            })
            .then((response) => {
                if (response !== undefined) {
                    setJob(response.data);
                }
            });
    };

    const handleUploadStandard = async () => {
        await reloadJobContent();
    };

    useEffect(() => {
        fetchToken();
        reloadJobContent();
    }, []);

    // Countdown timer logic
    useEffect(() => {
        if (countdown > 0) {
            const timer = setInterval(() => {
                setCountdown((prevCountdown) => prevCountdown - 1);
            }, 1000);
            return () => clearInterval(timer);
        } else if (countdown === 0 && job.flag_solving) {
            handleStop();
        }
    }, [countdown, job.flag_solving]);

    return (
        <AuthenticatedLayout
            user={auth?.user}
            header={
                <h2 className="font-semibold text-xl text-gray-800 leading-tight">
                    {`Job "${job?.name}"`}
                </h2>
            }
        >
            <Head>
                <Title>{`Job ${job?.name}`}</Title>
            </Head>

            <Content
                style={{
                    textAlign: 'center',
                    lineHeight: 4,
                }}
            >
                <Divider orientation="left">Upload schedule or preferences xlsx file</Divider>
                <Form onFinish={() => handleUploadStandard()} className="mt-4">
                    <Upload.Dragger
                        action={`/api/job/${job.id}/upload?_token=${token}`}
                        maxCount={1}
                        listType="picture"
                        accept=".xlsx"
                        onChange={() => handleUploadStandard()}
                    >
                        {t('step.fileUploadForm.dragFiles')}
                        <br />
                        <Space>
                            <Button>{t('Upload')}</Button>
                        </Space>
                    </Upload.Dragger>
                </Form>
                <Divider orientation="left">Solution</Divider>
                {job.error_message && (
                    <>
                        <Alert message="Last error" description={job.error_message} type="error" />
                        <br />
                    </>
                )}
                <Row gutter={16}>
                    <Col span={12}>
                        <Statistic
                            title="Sample"
                            value={job.flag_uploaded ? 'Uploaded' : 'Not uploaded'}
                            prefix={job.flag_uploaded ? <LikeOutlined /> : <DislikeOutlined />}
                        />
                    </Col>
                    <Col span={12}>
                        <Statistic
                            title="Solution"
                            value={
                                job.flag_solving
                                    ? `${countdown > 0 ? `Ready in ${countdown}s` : 'Not ready'}`
                                    : 'Ready'
                            }
                        />
                        <Space>
                            {!!job.flag_solving && (
                                <Button icon={<LoadingOutlined />} danger onClick={handleStop}>
                                    Stop solving
                                </Button>
                            )}
                            {!job.flag_solving && (
                                <Button icon={<PlayCircleOutlined />} onClick={handleSolve}>
                                    Solve
                                </Button>
                            )}
                            {!job.flag_solving && (
                                <Button
                                    type="primary"
                                    icon={<DownloadOutlined />}
                                    href={`/api/job/${job.id}/download?_token=${token}`}
                                    target="_blank"
                                >
                                    Download
                                </Button>
                            )}
                        </Space>
                    </Col>
                </Row>
            </Content>
        </AuthenticatedLayout>
    );
}
