import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import {Head, router} from '@inertiajs/react';
import {Avatar, Button, Col, Divider, Form, Layout, Row, Space, Upload} from 'antd';
import React, {useEffect, useState} from 'react';
import {useTranslation} from 'react-i18next';
import axios from 'axios';
import {useNotification} from "@/Providers/NotificationProvider.jsx";
import {useConfirmation} from '@/Providers/ConfirmationProvider.jsx';
import {DownloadOutlined} from "@ant-design/icons";

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
                console.log ( 'error response:', error);

                let errorMessage = error.response.data;
                if (errorMessage === undefined ||  typeof errorMessage !== 'string' ) {
                    errorMessage = error.message;
                }
                notifyError(errorMessage);
            })
            .then((response) => {
                if  ( response !== undefined ) {
                    notifySuccess(`Solving started`);
                    // setJobs([response.data, ...jobs]);
                }
            });
    };

    const handleStop = async () => {
        // const response =

        await axios.post(`/api/job/${job.id}/stop?_token=${token}`)
            .catch((error) => {
                console.log ( 'error response:', error);

                let errorMessage = error.response.data;
                if (errorMessage === undefined ||  typeof errorMessage !== 'string' ) {
                    errorMessage = error.message;
                }
                notifyError(errorMessage);
            })
            .then((response) => {
                if  ( response !== undefined ) {
                    notifySuccess(`Solving stop signal sent`);
                    // setJobs([response.data, ...jobs]);
                }
            });

    };

    const handleUploadPreferred = async () => {
        // TODO
    }
    const handleUploadStandard = async () => {
        // TODO
    }


    useEffect(() => {
        fetchToken();
    }, []);

    return (
        <AuthenticatedLayout
            user={auth.user}
            header={
                <h2 className="font-semibold text-xl text-gray-800 leading-tight">
                    {`Job "${job.name}"`}
                </h2>
            }
        >
            <Head>
                <Title>{`Job ${job.name}`}</Title>
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
                                <h3>Flags</h3>
                                Flag uploaded: {job.flag_uploaded? "yes":"no"};
                                Flag solving started: {job.flag_solving? "yes":"no"};
                                Flags solving done: {job.flag_solved? "yes":"no"};
                            </Col>
                        </Row>
                        <Row>
                            <Col>
                                <h3>Data statuses</h3>
                                Solver status:  TODO
                            </Col>
                        </Row>
                        <Row>
                            <Col>
                                <Divider orientation="left">Upload preferred timings xlsx file</Divider>
                                <Form
                                    onFinish={() => handleUploadPreferred()} className="mt-4"
                                    name={"prefered-upload-form"}>
                                    <Upload.Dragger
                                        action={`/api/job/${job.id}/upload-preferred?_token=${token}`}
                                        maxCount={1}
                                        listType="picture"
                                        accept=".xlsx"
                                        // onChange={() => handleUploadPreferred()}
                                    >
                                        {t('step.fileUploadForm.dragFiles')}
                                        <br/>
                                        <Space>
                                            <Button>{t('jobs.uploadPreferred')}</Button>
                                        </Space>
                                    </Upload.Dragger>
                                </Form>

                            </Col>
                            <Col>
                                <Divider orientation="left">Upload half solved sheet xlsx file</Divider>

                                <Form onFinish={() => handleUploadStandard()} className="mt-4">

                                    {/*TODO padaryti backe download example pagal paduotą tipą*/}
                                    {/*<p>Need a sample file? <a href="/download/school-example" target={'_blank'}>Click here</a></p>*/}
                                    <Upload.Dragger
                                        action={`/api/job/${job.id}/upload?_token=${token}`}
                                        maxCount={1}
                                        listType="picture"
                                        accept=".xlsx"
                                        // onChange={() => onFinish()}
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
                                        <Button size="large" onClick={handleSolve}>
                                            Solve
                                        </Button>
                                        <Button size="large" danger onClick={handleStop}>
                                            Stop solving
                                        </Button>
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
