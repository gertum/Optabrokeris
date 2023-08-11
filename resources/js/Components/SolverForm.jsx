import React, { useState } from 'react';
import { Card, Avatar, Row, Col, Form } from 'antd';
import { UserOutlined } from '@ant-design/icons';

const SolverwForm = ({ onFinish, children }) => {
    const [selectedCard, setSelectedCard] = useState(null);
    const [selectedTitle, setSelectedTitle] = useState('');

    const handleCardClick = (index, title) => {
        setSelectedCard(index);
        setSelectedTitle(title);
    };

    const cardData = [
        { title: 'Card 1 Title', description: 'Card 1 Description' },
        { title: 'Card 2 Title', description: 'Card 2 Description' },
        { title: 'Card 3 Title', description: 'Card 3 Description' },
        { title: 'Card 4 Title', description: 'Card 4 Description' },
        { title: 'Card 5 Title', description: 'Card 5 Description' },
        { title: 'Card 6 Title', description: 'Card 6 Description' },
        { title: 'Card 7 Title', description: 'Card 7 Description' },
        { title: 'Card 8 Title', description: 'Card 8 Description' },
    ];

    const splitIntoRows = (array, chunkSize) => {
        const rows = [];
        for (let i = 0; i < array.length; i += chunkSize) {
            rows.push(array.slice(i, i + chunkSize));
        }
        return rows;
    };

    const cardRows = splitIntoRows(cardData, 3);

    return (
        <Form onFinish={() => {
            if (selectedCard !== null) {
                onFinish({cardTitle: selectedTitle});
            } else {
                message.error('Please select a card before continuing.');
            }
        }} className="mt-4">
            {cardRows.map((row, rowIndex) => (
                <Row gutter={[16, 16]} key={rowIndex} className="mb-4">
                    {row.map((data, cardIndex) => (
                        <Col span={8} key={cardIndex}>
                            <Card
                                onClick={() => handleCardClick(cardIndex + rowIndex * 3, data.title)}
                                style={{
                                    display: 'flex',
                                    flexDirection: 'column',
                                    alignItems: 'center',
                                    justifyContent: 'center',
                                    border: selectedCard === cardIndex + rowIndex * 3 ? '2px solid blue' : '2px solid #ccc',
                                }}
                            >
                                <Avatar size={64} icon={<UserOutlined />} style={{ marginBottom: 12 }} />
                                <div style={{ textAlign: 'center' }}>
                                    <h3>{data.title}</h3>
                                    <p>{data.description}</p>
                                </div>
                            </Card>
                        </Col>
                    ))}
                </Row>
            ))}
            {children}
        </Form>
    );
};

export default SolverwForm;
