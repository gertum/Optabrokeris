import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link } from '@inertiajs/react';
import {Layout, Button, Avatar, Card, message, Spin, Space, Divider} from 'antd';
import {
    EyeOutlined,
    ReloadOutlined,
    EyeInvisibleOutlined,
    DownloadOutlined,
} from '@ant-design/icons';
import { useTranslation } from 'react-i18next';
import { format, parseISO } from 'date-fns';
import axios from 'axios';
import { useEffect, useState } from 'react';
// import JobsForm from '@/Pages/Jobs/View.jsx';

const { Content } = Layout;

export default function List({ auth }) {
    const { t } = useTranslation();
    const [token, setToken] = useState('');
    const [jobs, setJobs] = useState([]);
    const [currentPage, setCurrentPage] = useState(1);
    const [loadingJobs, setLoadingJobs] = useState(true);
    const [errorJobs, setErrorJobs] = useState(null);

    const jobsPerPage = 6;
    const startIndex = (currentPage - 1) * jobsPerPage;
    const endIndex = startIndex + jobsPerPage;

    const fetchJobs = async () => {
        try {
            setLoadingJobs(true);
            const jobsResponse = await axios.get('/api/job');
            setJobs(jobsResponse.data);
        } catch (error) {
            setErrorJobs(error.message);
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

    const handleResolve = async ({ id, name }) => {
        try {
            await axios.post(`/api/job/${id}/solve?_token=${token}`);
            message.success(`${name ? name : 'No Name'} is solving`, 5);
        } catch (error) {
            message.error(`HandleSolve error: ${error.message}`, 5);
        }
    };

    const displayedJobs = jobs.slice(startIndex, endIndex);

    useEffect(() => {
        fetchToken();
        fetchJobs();
    }, []);

    if (loadingJobs) {
        return (
            <AuthenticatedLayout
                user={auth.user}
                header={
                    <h2 className="font-semibold text-xl text-gray-800 leading-tight">
                        {t('jobs.createdJobs')}
                    </h2>
                }
            >
                <Head title="Jobs" />
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
                                    <Spin
                                        size="large"
                                        style={{
                                            textAlign: 'center',
                                            minHeight: 'calc(100vh - 128px)',
                                            lineHeight: 4,
                                        }}
                                    />
                                </div>
                            </div>
                        </div>
                    </div>
                </Content>
            </AuthenticatedLayout>
        );
    }

    if (errorJobs) {
        return (
            <AuthenticatedLayout
                user={auth.user}
                header={
                    <h2 className="font-semibold text-xl text-gray-800 leading-tight">
                        {t('jobs.createdJobs')}
                    </h2>
                }
            >
                <Head title="Jobs" />
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
                                    <div
                                        style={{
                                            textAlign: 'center',
                                            minHeight: 'calc(100vh - 128px)',
                                            lineHeight: 4,
                                        }}
                                    >
                                        <div>Error: {errorJobs}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </Content>
            </AuthenticatedLayout>
        );
    }

    if (!jobs.length) {
        return <JobsForm auth={auth} />;
    }

    return (
        <AuthenticatedLayout
            user={auth.user}
            header={
                <h2 className="font-semibold text-xl text-gray-800 leading-tight">
                    {t('jobs.createdJobs')}
                </h2>
            }
        >
            <Head title="Jobs" />
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
                                                    <h3>
                                                        {job?.name && job?.id
                                                            ? job.name[0].toUpperCase() +
                                                            job.name.substring(1)
                                                            : 'No Name'}
                                                    </h3>
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
                                                                <EyeInvisibleOutlined />
                                                            ) : (
                                                                <EyeOutlined />
                                                            )
                                                        }
                                                        size="large"
                                                    >
                                                        {!job.flag_uploaded ? 'Edit' : 'View'}
                                                    </Button>
                                                </Link>
                                                <Button
                                                    icon={<DownloadOutlined />}
                                                    size="large"
                                                    disabled={!job.flag_uploaded}
                                                    onClick={() =>
                                                        window.open(
                                                            `/api/job/${job.id}/download`,
                                                            '_blank'
                                                        )
                                                    }
                                                >
                                                    Download
                                                </Button>
                                                <Button
                                                    icon={<ReloadOutlined />}
                                                    size="large"
                                                    disabled={!job.flag_uploaded}
                                                    onClick={() =>
                                                        handleResolve({id: job.id, name: job.name})
                                                    }
                                                >
                                                    Rerun
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
