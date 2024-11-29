import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import {Head} from "@inertiajs/react";
import {Col, Divider, Layout, Row} from "antd";
import React from "react";

const {Content} = Layout;
const {Title} = Head;

export default function View({auth}) {
    return (
        <AuthenticatedLayout
            user={auth.user}
            header={
                <h2 className="font-semibold text-xl text-gray-800 leading-tight">
                    Downloads
                </h2>
            }
        >
            <Head>
                <Title>Downloads</Title>
            </Head>

            <Content
                style={{
                    textAlign: 'left',
                    lineHeight: 4,
                }}
            >
                <Row>
                    <Col xs={24}>
                        <p><a href={route('download.school.example')} target={'_blank'}>School example</a></p>
                        <p><a href={route('download.roster.subjects.example')} target={'_blank'}>Subjects list example</a></p>
                        <p><a href={route('download.roster.schedule.example')} target={'_blank'}>Hospital schedule example</a></p>
                        <p><a href={route('download.roster.preferred1.example')} target={'_blank'}>Hospital preferred times example 1</a></p>
                        <p><a href={route('download.roster.preferred2.example')} target={'_blank'}>Hospital preferred times example 2</a></p>
                    </Col>
                </Row>
                <Divider/>
            </Content>
        </AuthenticatedLayout>
    );
}