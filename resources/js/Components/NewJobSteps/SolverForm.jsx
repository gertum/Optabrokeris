// import React, {useState} from 'react';
// import {Card, Avatar, Row, Col, Form, message, Divider} from 'antd';
// import {useTranslation} from 'react-i18next';
//
// export const SolverForm = ({defaultType, readonly, onFinish, onSelect, children}) => {
//     const [selectedCard, setSelectedCard] = useState(defaultType);
//     const {t} = useTranslation();
//
//     const cardData = {
//         school: {
//             title: 'step.solverForm.school',
//             description: 'step.solverForm.schoolsScheduler',
//             value: 'school',
//         },
//         roster: {
//             title: 'step.solverForm.roster',
//             description:
//                 'step.solverForm.rosterScheduler',
//             value:
//                 'roster',
//         },
//         others: {
//             title: 'step.solverForm.others',
//             description:
//                 'step.solverForm.commingSoon',
//             value:
//                 'others',
//         }
//     };
//
//     if (readonly) {
//         return <>
//             <Divider orientation="left">Scenario</Divider>
//             <Card style={{padding: '10px'}} bodyStyle={{ padding: '8px' }}>
//                 <Row align="middle" gutter={16}>
//                     <Col>
//                         <Avatar className="bg-blue-500 text-bold" size={48}>
//                             {t(defaultType)[0].toUpperCase() + t(defaultType).substring(1)}
//                         </Avatar>
//                     </Col>
//                     <Col>
//                         <div style={{textAlign: "left"}}>
//                             <h3 style={{margin: 0}}>{t(cardData[defaultType].title)}</h3>
//                             <p style={{margin: 0}}>{t(cardData[defaultType].description)}</p>
//                         </div>
//                     </Col>
//                 </Row>
//             </Card>
//         </>;
//     }
//
//     const handleCardClick = (value) => {
//         if (value === 'others') {
//             message.warning('This card cannot be selected.', 5);
//         }
//
//         onSelect(value);
//         setSelectedCard(value);
//     };
//
//     const splitIntoRows = (array, chunkSize) => {
//         const rows = [];
//         for (let i = 0; i < array.length; i += chunkSize) {
//             rows.push(array.slice(i, i + chunkSize));
//         }
//         return rows;
//     };
//
//     const cardRows = splitIntoRows(Object.values(cardData), 3);
//
//     return <>
//         <Divider orientation="left">Choose scenario</Divider>
//         <Form
//             onFinish={() => {
//                 if (selectedCard === null) {
//                     message.error('Please select a card before continuing.', 5);
//                 }
//
//                 onFinish({type: selectedCard});
//             }}
//             className="mt-4"
//         >
//             {cardRows.map((row, rowIndex) => (
//                 <Row gutter={[16, 16]} key={rowIndex} className="mb-4">
//                     {row.map((data, cardIndex) => (
//                         <Col span={8} key={cardIndex}>
//                             <Card
//                                 onClick={() =>
//                                     handleCardClick(data.value)
//                                 }
//                                 style={{
//                                     display: 'flex',
//                                     flexDirection: 'column',
//                                     alignItems: 'center',
//                                     justifyContent: 'center',
//                                     cursor: 'pointer',
//                                     border:
//                                         selectedCard === data.value
//                                             ? '2px solid blue'
//                                             : '2px solid #ccc',
//                                 }}
//                                 bodyStyle={{padding: '8px'}}
//                             >
//                                 <Row align="middle" gutter={16}>
//                                     <Col>
//                                         <Avatar className="bg-blue-500 text-bold mb-2" size={64}>
//                                             {data?.title
//                                                 ? t(data.title)[0].toUpperCase() +
//                                                 t(data.title).substring(1)
//                                                 : data.value}
//                                         </Avatar>
//                                     </Col>
//                                     <Col>
//                                         <div style={{textAlign: "left"}}>
//                                             <h3>{t(data.title)}</h3>
//                                             <p>{t(data.description)}</p>
//                                         </div>
//                                     </Col>
//                                 </Row>
//                             </Card>
//                         </Col>
//                     ))}
//                 </Row>
//             ))}
//             {children}
//         </Form>
//     </>;
// };
