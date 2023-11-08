import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import {Head} from '@inertiajs/react';
import {Button, Col, Layout, message, Row, Space, Spin} from 'antd';
import {useState, useEffect} from 'react';
import {
    SolverForm,
    NamingForm,
    FileUploadForm,
    FinalForm,
} from '@/Components/NewJobSteps';
import {useTranslation} from 'react-i18next';
import axios from 'axios';

const {Content} = Layout;

export default function Form({auth, job: initialJob}) {
    const {t} = useTranslation();
    const [job, setJob] = useState(initialJob);
    const [current, setCurrent] = useState(0);
    const [token, setToken] = useState('');

    const handleValuesChange = (changedValues, allValues) => {
        setValues(allValues);
    };

    const handleSolverSelect = (index) => {
        setSolver(index);
    };

    const onFileUploadFinish = () => {
        setCurrent(current + 1);
    };

    const fetchToken = async () => {
        try {
            const tokenResponse = await axios.get('/login');
            setToken(tokenResponse.data);
        } catch (error) {
            message.error(`Login error: ${error.message}`, 5);
        }
    };

    useEffect(() => {
        fetchToken();
    }, []);

    return (
        <AuthenticatedLayout
            user={auth.user}
            header={
                <h2 className="font-semibold text-xl text-gray-800 leading-tight">
                    New job
                </h2>
            }
        >
            <Head title="New Job" />
            <Content
                style={{
                    textAlign: 'center',
                    lineHeight: 4,
                }}
            >
                <Row>
                    <Col xs={24}>
                        <SolverForm defaultType={job?.type} onSelect={handleSolverSelect} />
                        <NamingForm defaultValue={job?.name} onChange={handleValuesChange} />
                        {
                            job?.id &&  <FileUploadForm disabled={!!job}
                                                        onFinish={onFileUploadFinish}
                                                        onUploadChange={(file) => setFile(file)}
                                                        newJob={job}
                                                        token={token} />
                        }
                        {
                            job?.id && <FinalForm token={token}
                                                  job={job}
                                                  disabled={!!job}
                                                  solving={job && job.flag_solving} />
                        }
                    </Col>
                </Row>
                <Row justify="space-between">
                    <Col>
                        <Space>
                            <Button size="large" href={route('jobs.list')}>
                                Cancel
                            </Button>
                        </Space>
                    </Col>
                    <Col>
                        <Space>
                            <Button size="large" type="primary">
                                { job?.id ? 'Update job' : 'Create new job' }
                            </Button>
                        </Space>
                    </Col>
                </Row>
            </Content>
        </AuthenticatedLayout>
    );
}
