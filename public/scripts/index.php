<?php
$formConfig = array(
    'formdetails' => array(
        'title' => 'Login Form',
        'action' => 'http://127.0.0.1/my-app/public/scripts/handle.php',
        'method' => 'POST',
	'enctype' => "multipart/form-data",
	'errors' => array(),//add form errors
        'isAjax' => true,
        'validation_error_message' => 'Please fix the below validation error message'
    ),
    'fields' => array(
        'email' => array(
            'type' => 'input',
            'subtype' => 'email',
            'name' => 'email',
            'id' => 'email',
            'className' => 'form-control',
            'wrapperClass' => 'form-group',
            'value' => '',
            'placeholder' => 'Please enter email',
            'label' => 'Email:',
            'validation' => array(
                'required' => 'Email is required',
                'email' => 'Please enter a valid email address'
            )
        ),
//	'file' => array(
//            'type' => 'input',
//            'subtype' => 'file',
//            'name' => 'file',
//            'id' => 'file',
//            'className' => 'form-control',
//            'wrapperClass' => 'form-group',
//            'value' => '',
//            'placeholder' => 'Please upload a file',
//            'label' => 'Attachment:',
//            'validation' => array(
//                'frequired' => 'Attachment is required'                
//            )
//        ),
        'password' => array(
            'type' => 'input',
            'subtype' => 'password',
            'name' => 'password',
            'id' => 'password',
            'className' => 'form-control',
            'wrapperClass' => 'form-group',
            'value' => '',
            'placeholder' => 'Please enter password',
            'label' => 'Password:',
            'validation' => array(
                'required' => 'Password is required'
            )
        ),

//        'remember' => array(
//            'type' => 'checkbox',
//            'name' => 'remember',
//            'id' => 'remember',
//            'className' => '',
//	    'value' => array(1),
//            'label' => 'Select Remember Type',
//            'wrapperClass' => 'checkbox',
// 	    'validation' => array(
//                'required' => 'this is required field'
//            ),	    
//            'options' => array(
//                array(
//                    'value' => 1,
//                    'label' => 'Remember Me'
//                ),
//                array(
//                    'value' => 2,
//                    'label' => 'Never Remember Me'
//                ),
//                array(
//                    'value' => 3,
//                    'label' => 'Not now'
//                )
//            )          
//        ),
//
//        'gender' => array(
//            'type' => 'radio',
//            'name' => 'gender',
//            'id' => 'gender',
//            'className' => '',
//	    'value' => array('male'),
//            'label' => 'Gender',
//            'wrapperClass' => '',
// 	    'validation' => array(
//                'required' => 'this is required field'
//            ),	    
//            'options' => array(
//                array(
//                    'value' => 'male',
//                    'label' => 'Male'
//                ),
//                array(
//                    'value' => 'female',
//                    'label' => 'Female'
//                ),
//                array(
//                    'value' => 'other',
//                    'label' => 'Other'
//                )
//            )          
//        ),
//
//        'details' => array(
//            'type' => 'textarea',
//            'name' => 'details',
//            'id' => 'details',
//            'className' => 'form-control',
//	    'value' => 'This is the default value of this text area',
//            'label' => 'Details',
//            'wrapperClass' => 'form-group',
// 	    'validation' => array(
//                'required' => 'this is required field'
//            )  
//        ),
//        
//        'country' => array(
//            'type' => 'select',
//            'name' => 'country',
//            'id' => 'country',
//            'className' => 'form-control',
//            'label' => 'Select Remember Type',
//            'wrapperClass' => 'form-group',
//	    'value' => array('IND'),
//            'validation' => array(
//                'required' => 'this is required field'
//            ),
//            'multiple' => false,
//            'options' => array(
//array(
//                    'value' => '',
//                    'label' => 'Please select'
//                ),	
//                array(
//                    'value' => 'IND',
//                    'label' => 'India'
//                ),
//                array(
//                    'value' => 'US',
//                    'label' => 'United States'
//                ),
//                array(
//                    'value' => 'UK',
//                    'label' => 'United Kingdom'
//                )
//            ),
//        ),

        'submit' => array(
            'type' => 'submit',
            'name' => 'submit',
            'id' => 'submit',
            'className' => 'btn btn-default',
            'value' => 'Submit',
            'label' => 'Login',
        )
    ),
);
header("Access-Control-Allow-Origin: *");
header('Content-Type: application/json');
echo json_encode($formConfig);
exit();
?>
<script>
    var loginForm = <?php echo json_encode($formConfig); ?>;

    //console.log(loginForm.formdetails);
    var p = loginForm.inputs;
    for (var key in p) {
        if (p.hasOwnProperty(key)) {
            //console.log(key + " -> " + p[key].props);
            console.log(p[key].props);
        }
    }

</script>    
