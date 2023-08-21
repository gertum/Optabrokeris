import React, { useState } from 'react';
import { Card, Avatar, Row, Col, Form } from 'antd';
import { UserOutlined } from '@ant-design/icons';
import { useTranslation } from 'react-i18next';

export const SolverwForm = ({ onFinish, children }) => {
  const [selectedCard, setSelectedCard] = useState(null);
  const [selectedTitle, setSelectedTitle] = useState('');
  const { t } = useTranslation();

  const handleCardClick = (index, value) => {
    setSelectedCard(index);
    setSelectedTitle(value);
  };
  const cardData = [
    {
      title: 'step.solverForm.school',
      description: 'step.solverForm.schoolsScheduler',
      value: 'school',
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

  return (
    <Form
      onFinish={() => {
        if (selectedCard !== null) {
          onFinish({ type: selectedTitle });
        } else {
          message.error('Please select a card before continuing.');
        }
      }}
      className="mt-4"
    >
      {cardRows.map((row, rowIndex) => (
        <Row gutter={[16, 16]} key={rowIndex} className="mb-4">
          {row.map((data, cardIndex) => (
            <Col span={8} key={cardIndex}>
              <Card
                onClick={() =>
                  handleCardClick(cardIndex + rowIndex * 3, data.value)
                }
                style={{
                  display: 'flex',
                  flexDirection: 'column',
                  alignItems: 'center',
                  justifyContent: 'center',
                  border:
                    selectedCard === cardIndex + rowIndex * 3
                      ? '2px solid blue'
                      : '2px solid #ccc',
                }}
              >
                <Avatar
                  size={64}
                  icon={<UserOutlined />}
                  style={{ marginBottom: 12 }}
                />
                <div style={{ textAlign: 'center' }}>
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
  );
};
