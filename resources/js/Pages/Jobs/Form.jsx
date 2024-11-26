import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import {Head, router} from '@inertiajs/react';
import {Button, Col, Divider, Layout, Row, Space} from 'antd';
import {useEffect, useState} from 'react';
import {FileUploadForm, FinalForm, NamingForm, SolverForm,} from '@/Components/NewJobSteps';
import {useTranslation} from 'react-i18next';
import axios from 'axios';
import {useNotification} from "@/Providers/NotificationProvider.jsx";
import {useConfirmation} from '@/Providers/ConfirmationProvider.jsx';

const {Content} = Layout;

export default function Form({auth, job: initialJob}) {
    const {t} = useTranslation();
    const {notifySuccess, notifyError} = useNotification();
    const {requestConfirmation} = useConfirmation();
    const [job, setJob] = useState(initialJob);
    const [values, setValues] = useState({});
    const [current, setCurrent] = useState(0);
    const [solver, setSolver] = useState(job?.type);
    const [token, setToken] = useState('');
    const [creating, setCreating] = useState(!job);

    const handleValuesChange = (allValues) => {
        setValues(allValues);
        if (!creating) {
            handleSubmit(allValues);
        }
    };

    const handleSolverSelect = (index) => {
        setSolver(index);
    };

    const handleSubmit = (values) => {
        const data = {
            ...values,
            type: solver,
        };

        axios.request({
            method: job?.id ? 'PUT' : 'POST',
            url: job?.id ? `/api/job/${job.id}?_token=${token}` : `/api/job?_token=${token}`,
            data: data,
        }).then((response) => {
            setJob(response.data);
            notifySuccess(`Job ${job?.id ? 'updated' : 'created'} successfully`);
            if (creating) {
                router.visit(route('jobs.form', {job: response.data.id}));
            }
        });
    };

    const handleDelete = async () => {
        await requestConfirmation(
            'Confirm Delete',
            'Are you sure you want to delete this profile? Once deleted, this job cannot be recovered.'
        );

        axios.delete(`/api/job/${job.id}?_token=${token}`).then(() => {
            notifySuccess('Job deleted successfully');

            setJob(null);

            router.visit(route('jobs.list'));
        });
    };

    const onFileUploadFinish = () => {
        setCurrent(current + 1);
        reloadJob();
    };

    const onPreferedUploadFinish = () => {
        // setCurrent(current + 1);
        reloadJob();
    };

    const fetchToken = async () => {
        try {
            const tokenResponse = await axios.get('/login');
            setToken(tokenResponse.data);
        } catch (error) {
            notifyError(`Login error: ${error.message}`);
        }
    };

    const reloadJob = async () => {
        console.log('Reloading job...');
        axios.get(`/api/job/${job.id}`).then((response) => {
            setJob(response.data);
        });
    };

    useEffect(() => {
        fetchToken();
    }, []);

    useEffect(() => {
        if (job) {
            setCreating(false);
        }
    }, [job]);

    return (
        <AuthenticatedLayout
            user={auth.user}
            header={
                <h2 className="font-semibold text-xl text-gray-800 leading-tight">
                    {job?.id ? `Viewing "${job.name}" Profile` : "Creating profile"}
                </h2>
            }
        >
            <Head title={job?.id ? `` : "New profile form"}/>
            <Content
                style={{
                    textAlign: 'center',
                    lineHeight: 4,
                }}
            >
                <Row>
                    <Col xs={24}>
                        <SolverForm defaultType={job?.type} readonly={!!job} onSelect={handleSolverSelect}/>
                        <NamingForm defaultValue={job?.name} onChange={handleValuesChange} creating={creating}/>
                        {
                            job?.id && <FileUploadForm disabled={!!job}
                                                       onFinish={onFileUploadFinish}
                                                       newJob={job}
                                                       token={token}/>
                        }
                        {
                            job?.id && <FinalForm token={token}
                                                  job={job}
                                                  disabled={!!job}
                                                  solving={job && job.flag_solving && !job.flag_solved}
                                                  onStop={() => reloadJob()}
                                                  onSolve={() => reloadJob()}/>
                        }
                    </Col>
                </Row>
                <Divider/>
                <Row>
                    <Col>
                        <Divider orientation="left">Upload preferred timings xlsx file</Divider>
                                {/*<Form*/}
                                {/*    onFinish={() => onPreferedUploadFinish()} className="mt-4"*/}
                                {/*      name={"prefered-upload-form"} >*/}
                                    {/*<Upload.Dragger*/}
                                    {/*    action={`/api/job/${job.id}/upload-preferred?_token=${token}`}*/}
                                    {/*    maxCount={1}*/}
                                    {/*    listType="picture"*/}
                                    {/*    accept=".xlsx"*/}
                                    {/*    onChange={() => onPreferedUploadFinish()}*/}
                                    {/*>*/}
                                    {/*    {t('step.fileUploadForm.dragFiles')}*/}
                                    {/*    <br />*/}
                                    {/*    <Space>*/}
                                    {/*        <Button>{t('upload.preferred')}</Button>*/}
                                    {/*    </Space>*/}
                                    {/*</Upload.Dragger>*/}
                                {/*</Form>*/}

                        {/*<Form>*/}
                        {/*    <Button>todo upload</Button>*/}
                        {/*</Form>*/}
                        <p>TODO</p>
                    </Col>
                </Row>
                <Divider/>
                <Row justify="space-between">
                    <Col>
                        <Space>
                            <Button size="large" href={route('jobs.list')}>
                                Cancel
                            </Button>
                            {job?.id && (
                                <Button size="large" danger onClick={handleDelete}>
                                    {"Delete job"}
                                </Button>
                            )}
                        </Space>
                    </Col>
                    <Col>
                        <Space>
                            {!job && (
                                <Button size="large" type="primary" onClick={() => handleSubmit(values)}>
                                    {"Create new profile"}
                                </Button>
                            )}
                        </Space>
                    </Col>
                </Row>
            </Content>
        </AuthenticatedLayout>
    );
}
