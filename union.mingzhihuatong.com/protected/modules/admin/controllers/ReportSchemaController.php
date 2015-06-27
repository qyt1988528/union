<?php

class ReportSchemaController extends Controller {
    public function actionCreate() {
        $reportSchema = new ReportSchema();
        $attr = $_POST['Attr'];
        $reportSchema->adv_id = $attr['adv_id'];
        if($reportSchema->save()) {
            foreach($attr['fields'] as $cnt => $field) {
                $schemaField = new ReportSchemaFields();
                $schemaField->schema_id = $reportSchema->id;
                $schemaField->field = $field;
                $schemaField->position = $cnt + 1;
                $schemaField->save();
            }
        }
        echo json_encode(array(
            'code' => 0,
            'message' => 'ok',
        ));
    }
}
