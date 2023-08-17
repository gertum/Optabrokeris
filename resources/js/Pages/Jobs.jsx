import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link } from '@inertiajs/react';
import { Layout, Button, Avatar, Card } from "antd";
import { EditOutlined, EyeOutlined, ReloadOutlined } from '@ant-design/icons';
import { useTranslation } from "react-i18next";

const { Content } = Layout;

export default function Jobs({ auth, jobs }) {
    const { t } = useTranslation();

    const jobsTest = [
        { title: 'Card 1',
            createdAt: '2023-08-01',
            file: 'data 1.xlsx',
            solverIcon: <Avatar className="bg-blue-300 text-bold" size="large">Card 1</Avatar>
        },
        { title: 'Card 2',
            createdAt: '2023-08-05',
            file: 'data 2.xlsx',
            solverIcon: <Avatar className="bg-blue-300 text-bold" size="large">Card 2</Avatar>
        },
        { title: 'Card 3',
            createdAt: '2023-08-05',
            file: 'data 3.xlsx',
            solverIcon: <Avatar className="bg-blue-300 text-bold" size="large">Card 3</Avatar>
        },
        { title: 'Card 4',
            createdAt: '2023-08-05',
            file: 'data 4.xlsx',
            solverIcon: <Avatar className="bg-blue-300 text-bold" size="large">Card 4</Avatar>
        },
        { title: 'Card 5',
            createdAt: '2023-08-08',
            file: 'data 5.xlsx',
            solverIcon: <Avatar className="bg-blue-300 text-bold" size="large">Card 5</Avatar>
        },
        { title: 'Card 6',
            createdAt: '2023-08-08',
            file: 'data 6.xlsx',
            solverIcon: <Avatar className="bg-blue-300 text-bold" size="large">Card 6</Avatar>
        },
        { title: 'Card 7',
            createdAt: '2023-08-08',
            file: 'data 7.xlsx',
            solverIcon: <Avatar className="bg-blue-300 text-bold" size="large">Card 7</Avatar>
        },
        { title: 'Card 8',
            createdAt: '2023-08-05',
            file: 'data 8.xlsx',
            solverIcon: <Avatar className="bg-blue-300 text-bold" size="large">Card 8</Avatar>
        },
        { title: 'Card 9',
            createdAt: '2023-08-05',
            file: 'data 9.xlsx',
            solverIcon: <Avatar className="bg-blue-300 text-bold" size="large">Card 9</Avatar>
        },
        { title: 'Card 10',
            createdAt: '2023-08-05',
            file: 'data 10.xlsx',
            solverIcon: <Avatar className="bg-blue-300 text-bold" size="large">Card 10</Avatar>
        },
    ];

    return (
        <AuthenticatedLayout
            user={auth.user}
            header={<h2 className="font-semibold text-xl text-gray-800 leading-tight">{t('jobs.createdProfiles')}</h2>}
        >
            <Head title="Jobs" />
            <Content style={{
                textAlign: 'center',
                minHeight: 'calc(100vh - 128px)',
                lineHeight: 4,
            }}>
                <div className="py-12">
                    <div className="max-w-7xl mx-auto sm:px-6 lg:px-8">
                        <div className="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div className="p-6 text-gray-900">
                                {jobsTest.map((job, index) => (
                                    <Card key={index} className={`${jobsTest.length - 1 !== index ? "mb-4" : ""}`}>
                                        <div style={{display: 'flex', justifyContent: 'space-between', alignItems: 'center'}}>
                                            <div className="job-icon">
                                                <Avatar className="bg-blue-500 text-bold" size={64}>
                                                    {job.title}
                                                </Avatar>
                                            </div>
                                            <div className="job-info">
                                                <div className="job-text">
                                                    <h3>{job.title}</h3>
                                                    <p>Created at: {job.createdAt}</p>
                                                </div>
                                            </div>
                                            <div className="job-actions">
                                                {[
                                                    <Link
                                                        href={route('jobs.new', {
                                                            jobTitle: job.title,
                                                            createdAt: job.createdAt
                                                        })}
                                                        className="ant-btn ant-btn-lg"
                                                    >
                                                        <Button icon={<EditOutlined />} size="large">Edit</Button>
                                                    </Link>,
                                                    <Button icon={<EyeOutlined />} size="large">View</Button>,
                                                    <Button icon={<ReloadOutlined />} size="large">Rerun</Button>
                                                ]}
                                            </div>
                                        </div>
                                    </Card>
                                ))}
                            </div>
                        </div>
                    </div>
                </div>
            </Content>
            {/*{jobs.length ? jobs : <Content style={{*/}
            {/*    textAlign: 'center',*/}
            {/*    minHeight: 'calc(100vh - 128px)',*/}
            {/*    lineHeight: 4,*/}
            {/*}}>*/}
            {/*    <div>{t('jobs.noProfiles')}</div>*/}
            {/*    <Link href={route('jobs.new')}>*/}
            {/*        <Button shape="circle" style={{ height: 100, width: 100, display: 'inline-block', border: 'solid' }}>*/}
            {/*            <span style={{ whiteSpace: 'normal', textAlign: 'center' }}>{t('jobs.createNewProfile')}</span>*/}
            {/*        </Button>*/}
            {/*    </Link>*/}
            {/*</Content>}*/}
        </AuthenticatedLayout>
    );
}