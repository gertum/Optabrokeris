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
    const [values, setValues] = useState({});
    const [current, setCurrent] = useState(0);
    const [solver, setSolver] = useState(job?.type);
    const [token, setToken] = useState('');

    const handleValuesChange = (allValues) => {
        setValues(allValues);
    };

    const handleSolverSelect = (index) => {
        setSolver(index);
    };

    const handleSubmit = () => {
        const data = {
            ...values,
            type: solver,
        };

        console.log(data);

        axios.request({
            method: job?.id ? 'PUT' : 'POST',
            url: job?.id ? `/api/job/${job.id}?_token=${token}` : `/api/job?_token=${token}`,
            data: data,
        }).then((response) => {
            setJob(response.data);
            message.success(`Job ${job?.id ? 'updated' : 'created'} successfully`, 5);
        });
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

    const reloadJob = async () => {
        axios.get(`/api/job/${job.id}`).then((response) => {
            setJob(response.data);
        });
    }

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
                                                        newJob={job}
                                                        token={token} />
                        }
                        {
                            job?.id && <FinalForm token={token}
                                                  job={job}
                                                  disabled={!!job}
                                                  solving={job && job.flag_solving}
                                                  onStop={() => reloadJob()}
                                                  onSolve={() => reloadJob()} />
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
                            <Button size="large" type="primary" onClick={handleSubmit}>
                                { job?.id ? 'Update job' : 'Create new job' }
                            </Button>
                        </Space>
                    </Col>
                </Row>
            </Content>
        </AuthenticatedLayout>
    );
}
