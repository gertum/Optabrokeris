import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import {Head, router} from '@inertiajs/react';
import {Avatar, Button, Col, Divider, Form, Layout, Row, Space, Upload} from 'antd';
import React, {useEffect, useState} from 'react';
import {useTranslation} from 'react-i18next';
import axios from 'axios';
import {useNotification} from "@/Providers/NotificationProvider.jsx";
import {useConfirmation} from '@/Providers/ConfirmationProvider.jsx';
import {DownloadOutlined} from "@ant-design/icons";
// import {isStr} from "react-toastify/dist/utils";

const {Content} = Layout;

export default function View({auth, job: initialJob}) {
    const {t} = useTranslation();
    const {notifySuccess, notifyError} = useNotification();
    const {requestConfirmation} = useConfirmation();
    const [job, setJob] = useState(initialJob);
    // const [values, setValues] = useState({});
    // const [current, setCurrent] = useState(0);
    // const [solver, setSolver] = useState(job?.type);
    const [token, setToken] = useState('');
    // const [creating, setCreating] = useState(!job);

    // const handleValuesChange = (allValues) => {
    //     setValues(allValues);
    //     if (!creating) {
    //         handleSubmit(allValues);
    //     }
    // };
    //
    // const handleSolverSelect = (index) => {
    //     setSolver(index);
    // };

    // const handleSubmit = (values) => {
    //     const data = {
    //         ...values,
    //         type: solver,
    //     };
    //
    //     axios.request({
    //         method: job?.id ? 'PUT' : 'POST',
    //         url: job?.id ? `/api/job/${job.id}?_token=${token}` : `/api/job?_token=${token}`,
    //         data: data,
    //     }).then((response) => {
    //         setJob(response.data);
    //         notifySuccess(`Job ${job?.id ? 'updated' : 'created'} successfully`);
    //         if (creating) {
    //             router.visit(route('jobs.view', {job: response.data.id}));
    //         }
    //     });
    // };

    // const onFileUploadFinish = () => {
    //     setCurrent(current + 1);
    //     reloadJob();
    // };

    // const onPreferedUploadFinish = () => {
    //     // setCurrent(current + 1);
    //     reloadJob();
    // };


    // TODO use in component this function
    const fetchToken = async () => {
        try {
            const tokenResponse = await axios.get('/login');
            setToken(tokenResponse.data);
        } catch (error) {
            notifyError(`Login error: ${error.message}`);
        }
    };

    // const reloadJob = async () => {
    //     // console.log('Reloading job...');
    //     axios.get(`/api/job/${job.id}`).then((response) => {
    //         setJob(response.data);
    //     });
    // };

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

        // onSolve();
        // setSolving(true);
        // setTimeLeft(20);

        // return response.data;
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

        // onStop();
        // setSolving(false);
        // setTimerStarted(false);

        // return response.data;
    };


    useEffect(() => {
        fetchToken();
    }, []);

    // useEffect(() => {
    //     if (job) {
    //         setCreating(false);
    //     }
    // }, [job]);

    return (
        <AuthenticatedLayout
            user={auth.user}
            header={
                <h2 className="font-semibold text-xl text-gray-800 leading-tight">
                    {`Job "${job.name}"`}
                </h2>
            }
        >
            <Head title={`Job ${job.name}`}/>
            <Content
                style={{
                    textAlign: 'center',
                    lineHeight: 4,
                }}
            >

                <Row>
                    <Col xs={24}>
                        {/*<SolverForm defaultType={job?.type} readonly={!!job} onSelect={handleSolverSelect}/>*/}

                        <Row align="middle" gutter={16}>
                            <Col>
                                <Avatar className="bg-blue-500 text-bold" size={48}>
                                    {job.type}
                                </Avatar>
                            </Col>
                        </Row>
                        <Row>
                            <Col>
                                <Divider orientation="left">Upload preferred timings xlsx file</Divider>
                                <Form
                                    onFinish={() => onPreferedUploadFinish()} className="mt-4"
                                    name={"prefered-upload-form"}>
                                    <Upload.Dragger
                                        action={`/api/job/${job.id}/upload-preferred?_token=${token}`}
                                        maxCount={1}
                                        listType="picture"
                                        accept=".xlsx"
                                        onChange={() => onPreferedUploadFinish()}
                                    >
                                        {t('step.fileUploadForm.dragFiles')}
                                        <br/>
                                        <Space>
                                            <Button>{t('upload.preferred')}</Button>
                                        </Space>
                                    </Upload.Dragger>
                                </Form>

                            </Col>
                            <Col>
                                <Divider orientation="left">Upload half solved sheet xlsx file</Divider>

                                <Form onFinish={() => onFinish()} className="mt-4">

                                    {/*TODO padaryti backe download example pagal paduotą tipą*/}
                                    {/*<p>Need a sample file? <a href="/download/school-example" target={'_blank'}>Click here</a></p>*/}
                                    <Upload.Dragger
                                        action={`/api/job/${job.id}/upload?_token=${token}`}
                                        maxCount={1}
                                        listType="picture"
                                        accept=".xlsx"
                                        onChange={() => onFinish()}
                                    >
                                        {t('step.fileUploadForm.dragFiles')}
                                        <br/>
                                        <Space>
                                            <Button>{t('upload')}</Button>
                                        </Space>
                                    </Upload.Dragger>
                                </Form>

                            </Col>
                        </Row>

                        {/*{*/}
                        {/*    job?.id && <FinalForm token={token}*/}
                        {/*                          job={job}*/}
                        {/*                          disabled={!!job}*/}
                        {/*                          solving={job && job.flag_solving && !job.flag_solved}*/}
                        {/*                          onStop={() => reloadJob()}*/}
                        {/*                          onSolve={() => reloadJob()}/>*/}
                        {/*}*/}


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
