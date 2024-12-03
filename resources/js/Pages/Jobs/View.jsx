import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import {Head} from '@inertiajs/react';
import {Avatar, Button, Col, Divider, Form, Layout, Row, Space, Upload, Statistic} from 'antd';
import React, {useEffect, useState} from 'react';
import {useTranslation} from 'react-i18next';
import axios from 'axios';
import {useNotification} from "@/Providers/NotificationProvider.jsx";
import {useConfirmation} from '@/Providers/ConfirmationProvider.jsx';
import {DownloadOutlined, DislikeOutlined, LikeOutlined, CheckOutlined} from "@ant-design/icons";

const {Content} = Layout;
const {Title} = Head;


export default function View({auth, job: initialJob}) {
    const {t} = useTranslation();
    const {notifySuccess, notifyError} = useNotification();
    const {requestConfirmation} = useConfirmation();
    const [job, setJob] = useState(initialJob);
    const [token, setToken] = useState('');


    // TODO use in component this function
    const fetchToken = async () => {
        try {
            const tokenResponse = await axios.get('/login');
            setToken(tokenResponse.data);
        } catch (error) {
            notifyError(`Login error: ${error.message}`);
        }
    };

    const handleSolve = async () => {
        // const response = await
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
                console.log('solve start response', response);
                if (response !== undefined) {
                    notifySuccess(`Solving started`);

                    // be sure the response is a job object
                    if (response.data.id !== undefined) {
                        setJob(response.data);
                        reloadAfterSomeTime(31);
                    }
                }
            });
    };

    const handleStop = async () => {
        // const response =

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
                    notifySuccess(`Solving stop signal sent`);
                    reloadJobContent();
                }
            });

    };

    const reloadJobContent = async () => {
        await axios.request({
                method: 'GET',
                url: `/api/job/${job.id}?_token=${token}`,
            }
        )
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
                    // notifySuccess(`Reload job successful`);
                    setJob(response.data);
                }
            });

    }

    const handleUploadStandard = async () => {
        // yet we didn't find a way to get upload result, so we make additional request to get job content
        await reloadJobContent();
    }

    const reloadAfterSomeTime = async (time) => {
        console.log('Page will be reloaded in ' + time);
        setTimeout(() => {
            reloadJobContent();
            console.log('Page reloaded');
        }, time * 1000);
    }

    useEffect(() => {
        fetchToken();
        reloadJobContent();
    }, []);

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

                <Row>
                    <Col xs={24}>

                        <Row align="middle" gutter={16}>
                            <Col>
                                <Avatar className="bg-blue-500 text-bold" size={48}>
                                    {job.type}
                                </Avatar>
                            </Col>
                        </Row>
                        <Row>
                            <Col>
                                <h3>Status</h3>
                                <Statistic title="Uploaded" value={job.flag_uploaded?'+':'-'} prefix={job.flag_uploaded?<LikeOutlined />:<DislikeOutlined/> } />
                                <Statistic title="Solving started" value={job.flag_solving?'+':'-'} prefix={job.flag_solving?<LikeOutlined />:<DislikeOutlined/> } />
                                <Statistic title="Solving DONE" value={job.flag_solved?'+':'-'} prefix={job.flag_solved?<CheckOutlined />:<DislikeOutlined/> } />
                                <Statistic title="Solver status" value={job.status}  />
                                <Statistic title="Last error" value={job.error_message}  />
                            </Col>
                        </Row>
                        <Row>
                            <Col>
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
                                        <br/>
                                        <Space>
                                            <Button>{t('Upload')}</Button>
                                        </Space>
                                    </Upload.Dragger>
                                </Form>

                            </Col>
                        </Row>

                        <Row>
                            <Col>
                                <Divider orientation="left">Solution</Divider>
                                <div className="my-2">
                                    <Space>
                                        {
                                            job.status !== 'SOLVING_ACTIVE' ?
                                                <Button size="large" onClick={handleSolve}>
                                                    Solve
                                                </Button>
                                                :
                                                <Button size="large" danger onClick={handleStop}>
                                                    Stop solving
                                                </Button>
                                        }
                                    </Space>
                                </div>

                                <Button
                                    type="primary"
                                    shape="round"
                                    icon={<DownloadOutlined/>}
                                    size="large"
                                    href={`/api/job/${job.id}/download?_token=${token}`}
                                    target="_blank"
                                >
                                    Download
                                </Button>
                            </Col>
                        </Row>
                    </Col>
                </Row>
                <Divider/>
            </Content>
        </AuthenticatedLayout>
    );
}
