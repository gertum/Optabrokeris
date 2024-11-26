import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import {Head, router} from '@inertiajs/react';
import {Button, Col, Divider, Layout, Row, Space, Form, Upload} from 'antd';
import {useEffect, useState} from 'react';
const {Content} = Layout;


export default function Create({auth}) {
    return (
        <AuthenticatedLayout
            user={auth.user}
            header={
                <h2 className="font-semibold text-xl text-gray-800 leading-tight">
                    {"New job"}
                </h2>
            }
        >
            <Head title={"New job"}/>
            <Content
                style={{
                    textAlign: 'center',
                    lineHeight: 4,
                }}
            >
                <Row>
                    <Col xs={24}>
                        TODO create job
                    </Col>
                </Row>
            </Content>
        </AuthenticatedLayout>
    );
}