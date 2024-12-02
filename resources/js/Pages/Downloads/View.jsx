import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import {Head} from "@inertiajs/react";
import {Col, Divider, Layout, Row, Table} from "antd";
import React from "react";

const {Content} = Layout;
const {Title} = Head;

export default function ExampleView({auth}) {
    const columns = [
        {
            title: 'Title',
            dataIndex: 'title',
            key: 'title',
        },
        {
            title: 'Description',
            dataIndex: 'description',
            key: 'description',
        },
        {
            title: 'Download',
            dataIndex: 'url',
            key: 'url',
            render: (url) => (
                <a href={url} download>Download</a>
            ),
        },
    ];
    const links = [
        {
            title: 'School example',
            url: route('download.school.example'),
            description: 'Basic example for rostering school activities',
        },
        {
            title: 'Subjects list example',
            url: route('download.roster.subjects.example'),
            description: 'Example of subjects list for rostering',
        },
        {
            title: 'Hospital schedule example',
            url: route('download.roster.schedule.example'),
            description: 'Example of hospital schedule for rostering',
        },
        {
            title: 'Hospital preferred times example 1',
            url: route('download.roster.preferred1.example'),
            description: 'Example of hospital preferred times for rostering',
        },
        {
            title: 'Hospital preferred times example 2',
            url: route('download.roster.preferred2.example'),
            description: 'Example of hospital preferred times for rostering',
        },
    ];

    return (
        <AuthenticatedLayout
            user={auth.user}
            header={
                <h2 className="font-semibold text-xl text-gray-800 leading-tight">
                    Examples
                </h2>
            }
        >
            <Head>
                <Title>Examples</Title>
            </Head>

            <Content
                style={{
                    textAlign: 'left',
                    lineHeight: 4,
                }}
            >
                <Table dataSource={links} columns={columns} rowKey="title" />
            </Content>
        </AuthenticatedLayout>
    );
}
