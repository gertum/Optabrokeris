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
                        <p>School example: </p>
                        <p>Hospital schedule example: </p>
                        <p>Hospital preferences example: </p>
                        <p>Subjects list example: </p>
                    </Col>
                </Row>
                <Divider/>
            </Content>
        </AuthenticatedLayout>
    );
}