import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import {Head} from '@inertiajs/react';
import {Button, Divider, Form, Layout, message, Space, Table, Upload} from 'antd';
import {useTranslation} from 'react-i18next';
import axios from 'axios';
import {useEffect, useState} from 'react';

const {Content} = Layout;
const {Title} = Head;

export default function SubjectList({auth}) {
    const {t} = useTranslation();
    const [subjects, setSubjects] = useState([]);
    const [searchName, setSearchName] = useState([]);
    const [token, setToken] = useState('');
    const tableColumns = [
        {
            title: 'Name',
            dataIndex: 'name',
            key: 'name',
            filterSearch: true,
        },
        {
            title: 'Hours per day',
            dataIndex: 'hours_in_day',
            key: 'hours_in_day',
        },
        {
            title: 'Hours per month',
            dataIndex: 'hours_in_month',
            key: 'hours_in_month',
        },
        {
            title: 'Position amount',
            dataIndex: 'position_amount',
            key: 'position_amount',
        },
    ];

    const fetchSubjects = async () => {
        try {
            // setLoadingJobs(true);
            const subjectsResponse = await axios.get('/api/subject', {params: {name: searchName}});
            setSubjects(subjectsResponse.data);
        } catch (error) {
            // setErrorJobs(error.message);
            message.error(`Subjects load error: ${error.message}`, 5);

        } finally {
            // setLoadingJobs(false);
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

    const onFileUploadFinish = () => {
        fetchSubjects();
    };

    useEffect(
        () => {
            fetchToken();
            fetchSubjects();
        }, []
    );


    useEffect(
        () => {
            fetchSubjects();
        },
        [searchName]
    );

    const onChange = (pagination, filters, sorter, extra) => {
        console.log('params', pagination, filters, sorter, extra);
    };

    return (
        <AuthenticatedLayout
            user={auth.user}
            header={
                <h2 className="font-semibold text-xl text-gray-800 leading-tight">
                    {t('subjects.list')}
                </h2>
            }
        >
            <Head>
                <Title>{t('subjects.list')}</Title>
            </Head>
            <Content
                style={{
                    textAlign: 'center',
                    minHeight: 'calc(100vh - 128px)',
                    lineHeight: 4,
                }}
            >
                <Divider orientation="left">Upload via file</Divider>
                <Form onFinish={() => onFileUploadFinish()} className="mt-4">
                    <Upload.Dragger
                        action={`/api/subject/upsert-xlsx?_token=${token}`}
                        maxCount={1}
                        listType="picture"
                        accept=".xlsx"
                        onChange={() => onFileUploadFinish()}
                    >
                        {t('step.fileUploadForm.dragFiles')}
                        <br />
                        <Space>
                            <Button>{t('upload')}</Button>
                        </Space>
                    </Upload.Dragger>
                </Form>
                <Divider orientation="left">Overview</Divider>
                <Table dataSource={subjects} columns={tableColumns} onChange={onChange} rowKey="name" />
            </Content>
        </AuthenticatedLayout>
    );
}
