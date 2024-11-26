import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import {Head} from '@inertiajs/react';
import {Button, Divider, Form, Layout, message, Space, Upload} from 'antd';
import {useTranslation} from 'react-i18next';
import axios from 'axios';
import {useEffect, useState} from 'react';
import TextInput from '@/Components/TextInput';

const {Content} = Layout;

export default function List({auth}) {
    const {t} = useTranslation();
    const [subjects, setSubjects] = useState([]);
    const [searchName, setSearchName] = useState([]);
    const [token, setToken] = useState('');

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

    useEffect(() => {
        fetchToken();
        fetchSubjects();
    }, []);


    useEffect(() => {
        fetchSubjects();
        },
        [searchName]
    );
    return (
        <AuthenticatedLayout
            user={auth.user}
            header={
                <h2 className="font-semibold text-xl text-gray-800 leading-tight">
                    {t('subjects.list')}
                </h2>
            }
        >
            <Head title="Subjects"/>
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


                                <Divider orientation="left">Upload subjects file</Divider>
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

                                <h1>Subjects</h1>
                                <TextInput
                                    value={searchName || ""}
                                    onChange={(e) => setSearchName(e.target.value)}
                                    placeholder="Search by name..."
                                />

                                <table>
                                    <thead>
                                    <th>No.</th>
                                    <th>Name</th>
                                    <th>Hours per day</th>
                                    <th>Hours per month</th>
                                    <th>Position amount</th>
                                    </thead>
                                    {subjects.map((subject, index) => (
                                        <tr>
                                            <td>{index}</td>
                                            <td>{subject.name}</td>
                                            <td>{subject.hours_in_day}</td>
                                            <td>{subject.hours_in_month}</td>
                                            <td>{subject.position_amount}</td>
                                        </tr>
                                    ))}
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </Content>
        </AuthenticatedLayout>
    );
}