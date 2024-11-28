import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import {Head, Link} from '@inertiajs/react';
import {Avatar, Button, Card, Form, Input, Layout, message, Select, Space} from 'antd';
import {DownloadOutlined, EyeInvisibleOutlined, EyeOutlined, ReloadOutlined,} from '@ant-design/icons';
import {useTranslation} from 'react-i18next';
import {format, parseISO} from 'date-fns';
import axios from 'axios';
import {useEffect, useState} from 'react';
import {useNotification} from "@/Providers/NotificationProvider.jsx";
import {useConfirmation} from '@/Providers/ConfirmationProvider.jsx';

const {Content} = Layout;
const {Option} = Select;
const {Title} = Head;

export default function List({auth}) {
    const {t} = useTranslation();
    const [token, setToken] = useState('');
    const [jobs, setJobs] = useState([]);
    const [currentPage, setCurrentPage] = useState(1);
    const [loadingJobs, setLoadingJobs] = useState(true);

    // TODO daryti backendinį puslapiavimą
    const jobsPerPage = 6;
    const startIndex = (currentPage - 1) * jobsPerPage;
    const endIndex = startIndex + jobsPerPage;
    const {notifySuccess, notifyError} = useNotification();
    const {requestConfirmation} = useConfirmation();

    const fetchJobs = async () => {
        try {

            // TODO paging in backend
            setLoadingJobs(true);
            const jobsResponse = await axios.get('/api/job');
            setJobs(jobsResponse.data);
        } catch (error) {
            // setErrorJobs(error.message);
            notifyError(error.message);
        } finally {
            setLoadingJobs(false);
        }
    };

    const fetchToken = async () => {
        try {
            const tokenResponse = await axios.get('/login');
            setToken(tokenResponse.data);
        } catch (error) {
            message.error(`Login error: ${error.message}`, 5);
        }
    };

    const createJob = async (values) => {
        console.log('create job clicked with values', values);

        const data = {
            ...values,
        };

            axios.request({
                method: 'POST',
                url: `/api/job?_token=${token}`,
                data: data,
            }).catch((error) => {
                console.log ( 'error response:', error.response.data);
                notifyError(error.response.data);
            }).then((response) => {
                if  ( response !== undefined ) {
                    notifySuccess(`Job created successfully`);
                    setJobs([response.data, ...jobs]);
                }
            });
    }

    const deleteJob = async (jobId) => {
        let confirmed = await requestConfirmation(
            'Confirm Delete',
            'Are you sure you want to delete this profile? Once deleted, this job cannot be recovered.'
        );

        console.log('confirmed:', confirmed);

        if ( !confirmed) {
            return;
        }

        axios.request({
            method: 'DELETE',
            url: `/api/job/${jobId}/?_token=${token}`,
        }).catch((error) => {
            console.log ( 'error response:', error.response.data);
            notifyError(error.response.data);
        }).then((response) => {
            if  ( response !== undefined ) {
                notifySuccess(`Job deleted successfully`);
                fetchJobs();
            }
        });
    }

    const displayedJobs = jobs.slice(startIndex, endIndex);

    useEffect(() => {
        fetchToken();
        fetchJobs();
    }, []);

    // ОК
    return (
        <AuthenticatedLayout
            user={auth.user}
            header={
                <h2 className="font-semibold text-xl text-gray-800 leading-tight">
                    {t('jobs.createdJobs')}
                </h2>
            }
        >
            <Head>
                <Title>Jobs</Title>
            </Head>
            <Content
                style={{
                    textAlign: 'center',
                    minHeight: 'calc(100vh - 128px)',
                    lineHeight: 4,
                }}
            >
                <div className="py-6">
                    <div className="mx-auto sm:px-6 lg:px-8">
                        <div className="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div className="p-6 text-gray-900">
                                <Form onFinish={(values) => createJob(values)}>
                                    <Form.Item
                                        label={"New Job name"}
                                        name="name"
                                        rules={[
                                            {
                                                required: true,
                                                message: "Input job name",
                                            },
                                        ]}
                                    >
                                        <Input size="medium"/>
                                    </Form.Item>

                                    <Form.Item
                                        label="Job type"
                                        name="type"
                                        rules={[{required: true, message: 'Please input!'}]}
                                    >
                                        <Select>
                                            <Option value={"roster"}>Roster</Option>
                                            <Option value={"school"}>School</Option>
                                        </Select>
                                    </Form.Item>

                                    <Form.Item label={null}>
                                        <Button type="primary" htmlType="submit">
                                            Create
                                        </Button>
                                    </Form.Item>

                                </Form>

                                {displayedJobs.map((job, index) => (
                                    <Card
                                        key={job.id}
                                        style={{marginBottom: jobs.length - 1 !== index ? '1.5rem' : '0'}}
                                    >
                                        <div
                                            style={{
                                                display: 'flex',
                                                justifyContent: 'space-between',
                                                alignItems: 'center',
                                            }}
                                        >
                                            <div className="job-icon">
                                                <Avatar className="bg-blue-500 text-bold" size={64}>
                                                    {job?.type
                                                        ? job.type[0].toUpperCase() + job.type.substring(1)
                                                        : '-'}
                                                </Avatar>
                                            </div>
                                            <div className="job-info">
                                                <div className="job-text">
                                                    <h3>{job.name }</h3>
                                                    <p>
                                                        Created at:
                                                        {job?.created_at
                                                            ? format(
                                                                parseISO(job.created_at),
                                                                'yyyy-MM-dd HH:mm:ss'
                                                            )
                                                            : '-'}
                                                    </p>
                                                </div>
                                            </div>
                                            <Space className="job-actions">
                                                <Link
                                                    href={route('jobs.view', {
                                                        id: job.id,
                                                    })}
                                                    className="ant-btn ant-btn-lg"
                                                >
                                                    <Button
                                                        icon={
                                                            !job.flag_uploaded ? (
                                                                <EyeInvisibleOutlined/>
                                                            ) : (
                                                                <EyeOutlined/>
                                                            )
                                                        }
                                                        size="large"
                                                    >
                                                        {'View'}
                                                    </Button>
                                                </Link>
                                                <Button
                                                    size="large"
                                                    onClick={() => deleteJob(job.id)}
                                                >
                                                    Delete
                                                </Button>

                                            </Space>
                                        </div>
                                    </Card>
                                ))}
                            </div>
                            <div className="p-6 text-gray-900">
                                <Space>
                                    <Button
                                        disabled={currentPage === 1}
                                        onClick={() => setCurrentPage(currentPage - 1)}
                                    >
                                        Previous
                                    </Button>
                                    <Button
                                        disabled={endIndex >= jobs.length}
                                        onClick={() => setCurrentPage(currentPage + 1)}
                                    >
                                        Next
                                    </Button>
                                </Space>
                            </div>
                        </div>
                    </div>
                </div>
            </Content>
        </AuthenticatedLayout>
    );
}
