// import React, {useMemo} from 'react';
// import {Divider, Form, Input} from 'antd';
// import {useTranslation} from 'react-i18next';
// import debounce from 'lodash.debounce';
//
// export const NamingForm = ({onChange, defaultValue, children, creating}) => {
//     const {t} = useTranslation();
//
//     const enterNameLabel = t('step.namingForm.enterName');
//     const pleaseEnterNameMessage = t('step.namingForm.pleaseEnterJobName');
//
//     const debouncedOnChange = useMemo(() => debounce(onChange, 1), [onChange]);
//
//     const handleValuesChange = (changedValues, allValues) => {
//         console.log(creating);
//
//         if (creating) {
//             onChange(allValues);
//
//             return;
//         }
//
//         debouncedOnChange(allValues);
//     };
//
//     return <>
//         <Divider orientation="left">Name of a job</Divider>
//         <Form onValuesChange={handleValuesChange} initialValues={{name: defaultValue}}>
//             <Form.Item
//                 label={false} // Use the translated label
//                 name="name"
//                 rules={[
//                     {
//                         required: true,
//                         message: pleaseEnterNameMessage, // Use the translated message
//                     },
//                 ]}
//             >
//                 <Input
//                     size="large"
//                     placeholder={enterNameLabel} // Use the translated label as placeholder
//                 />
//             </Form.Item>
//             {children}
//         </Form>
//     </>;
// };
