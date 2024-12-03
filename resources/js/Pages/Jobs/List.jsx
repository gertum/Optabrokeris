import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import {Head} from '@inertiajs/react';
import {Avatar, Button, Col, Form, Input, Layout, message, Modal, Row, Select, Skeleton, List} from 'antd';
import {DeleteOutlined, EyeInvisibleOutlined, EyeOutlined, FileAddOutlined} from '@ant-design/icons';
import {useTranslation} from 'react-i18next';
import axios from 'axios';
import {useEffect, useState} from 'react';
import {useNotification} from "@/Providers/NotificationProvider.jsx";
import {useConfirmation} from '@/Providers/ConfirmationProvider.jsx';

const {Content} = Layout;
const {Option} = Select;
const {Title} = Head;

export default function JobList({auth}) {
    const {t} = useTranslation();
    const [token, setToken] = useState('');
    const [jobs, setJobs] = useState([]);
    const [loadingJobs, setLoadingJobs] = useState(true);
    const [isModalOpen, setIsModalOpen] = useState(false);
    const {notifySuccess, notifyError} = useNotification();
    const {requestConfirmation} = useConfirmation();
    const [form] = Form.useForm();

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
            console.log('error response:', error.response.data);
            notifyError(error.response.data);
        }).then((response) => {
            if (response !== undefined) {
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

        if (!confirmed) {
            return;
        }

        axios.request({
            method: 'DELETE',
            url: `/api/job/${jobId}/?_token=${token}`,
        }).catch((error) => {
            console.log('error response:', error.response.data);
            notifyError(error.response.data);
        }).then((response) => {
            if (response !== undefined) {
                notifySuccess(`Job deleted successfully`);
                fetchJobs();
            }
        });
    }

    useEffect(() => {
        fetchToken();
        fetchJobs();
    }, []);

    const showModal = () => {
        setIsModalOpen(true);
    };
    const handleOk = () => {
        form.submit();
        setIsModalOpen(false);
    };
    const handleCancel = () => {
        setIsModalOpen(false);
    };

    return (
        <AuthenticatedLayout
            user={auth.user}
            header={
                <Row align={"middle"}>
                    <Col span={4}></Col>
                    <Col span={16}>
                        <h2 className="font-semibold text-xl text-gray-800 leading-tight">
                            {t('Profiles')}
                        </h2>
                    </Col>
                    <Col span={4} className={"text-right"}>
                        <Button type="primary" icon={<FileAddOutlined />} onClick={showModal}>
                            Create
                        </Button>
                    </Col>
                </Row>
            }
        >
            <Head>
                <Title>Profiles</Title>
            </Head>
            <Content
                style={{
                    minHeight: 'calc(100vh - 128px)',
                    lineHeight: 4,
                }}
            >
                <Modal title="Create new profile" open={isModalOpen} onOk={handleOk} onCancel={handleCancel}>
                    <Form
                        form={form}
                        labelCol={{
                            span: 8,
                        }}
                        wrapperCol={{
                            span: 16,
                        }}
                        style={{
                            maxWidth: 600,
                        }}
                        onFinish={(values) => createJob(values)}>
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
                    </Form>
                </Modal>
                <List
                    loading={loadingJobs}
                    itemLayout="horizontal"
                    dataSource={jobs}
                    renderItem={(job) => (
                        <List.Item
                            actions={[
                                <Button
                                    href={route('jobs.view', {
                                        id: job.id,
                                    })}
                                    icon={
                                        !job.flag_uploaded ? (
                                            <EyeInvisibleOutlined/>
                                        ) : (
                                            <EyeOutlined/>
                                        )
                                    }
                                    type="link"
                                    size="small"
                                >
                                    {'View'}
                                </Button>,
                                <Button
                                    danger
                                    type="link"
                                    size="small"
                                    icon={<DeleteOutlined/>}
                                    onClick={() => deleteJob(job.id)}
                                >
                                    Delete
                                </Button>
                            ]}
                        >
                            <Skeleton avatar title={false} loading={loadingJobs} active>
                                <List.Item.Meta
                                    avatar={
                                        <Avatar className="bg-blue-500 text-bold" size={64}>
                                            {job?.type
                                                ? job.type[0].toUpperCase() + job.type.substring(1)
                                                : '-'}
                                        </Avatar>
                                    }
                                    title={job.name}
                                    description="-"
                                />
                            </Skeleton>
                        </List.Item>
                    )}
                />
            </Content>
        </AuthenticatedLayout>
    );
}
