import React, {useState} from 'react';
import {Card, Avatar, Row, Col, Form, message, Divider} from 'antd';
import {useTranslation} from 'react-i18next';

export const SolverForm = ({defaultType, onFinish, onSelect, children}) => {
    const [selectedCard, setSelectedCard] = useState(defaultType);
    const {t} = useTranslation();

    const handleCardClick = (value) => {
        if (value === 'others') {
            message.warning('This card cannot be selected.', 5);
        }

        console.log(value);

        onSelect(value);
        setSelectedCard(value);
    };

    const cardData = [
        {
            title: 'step.solverForm.school',
            description: 'step.solverForm.schoolsScheduler',
            value: 'school',
        },
        {
            title: 'step.solverForm.roster',
            description: 'step.solverForm.rosterScheduler',
            value: 'roster',
        },
        {
            title: 'step.solverForm.others',
            description: 'step.solverForm.commingSoon',
            value: 'others',
        },
    ];

    const splitIntoRows = (array, chunkSize) => {
        const rows = [];
        for (let i = 0; i < array.length; i += chunkSize) {
            rows.push(array.slice(i, i + chunkSize));
        }
        return rows;
    };

    const cardRows = splitIntoRows(cardData, 3);

    return <>
        <Divider orientation="left">Choose scenario</Divider>
        <Form
            onFinish={() => {
                if (selectedCard === null) {
                    message.error('Please select a card before continuing.', 5);
                }

                onFinish({type: selectedCard});
            }}
            className="mt-4"
        >
            {cardRows.map((row, rowIndex) => (
                <Row gutter={[16, 16]} key={rowIndex} className="mb-4">
                    {row.map((data, cardIndex) => (
                        <Col span={8} key={cardIndex}>
                            <Card
                                onClick={() =>
                                    handleCardClick(data.value)
                                }
                                style={{
                                    display: 'flex',
                                    flexDirection: 'column',
                                    alignItems: 'center',
                                    justifyContent: 'center',
                                    cursor: 'pointer',
                                    border:
                                        selectedCard === data.value
                                            ? '2px solid blue'
                                            : '2px solid #ccc',
                                }}
                            >
                                <Avatar className="bg-blue-500 text-bold mb-2" size={64}>
                                    {data?.title
                                        ? t(data.title)[0].toUpperCase() +
                                        t(data.title).substring(1)
                                        : data.value}
                                </Avatar>
                                <div style={{textAlign: 'center'}}>
                                    <h3>{t(data.title)}</h3>
                                    <p>{t(data.description)}</p>
                                </div>
                            </Card>
                        </Col>
                    ))}
                </Row>
            ))}
            {children}
        </Form>
    </>;
};
