<?php
namespace fay\models\tables;

use fay\core\db\Table;

class ExamPaperQuestions extends Table{
	protected $_name = 'exam_paper_questions';
	protected $_primary = array('paper_id', 'question_id');
	
	/**
	 * @return ExamPaperQuestions
	 */
	public static function model($className=__CLASS__){
		return parent::model($className);
	}
	
	public function rules(){
		return array(
			array(array('paper_id', 'question_id'), 'int', array('min'=>0, 'max'=>16777215)),
			array(array('sort'), 'int', array('min'=>0, 'max'=>255)),
			array(array('score'), 'float', array('length'=>5, 'decimal'=>2)),
		);
	}

	public function labels(){
		return array(
			'paper_id'=>'试卷编号',
			'question_id'=>'试题编号',
			'score'=>'分值',
			'sort'=>'排序值',
		);
	}

	public function filters(){
		return array(
			'paper_id'=>'intval',
			'question_id'=>'intval',
			'score'=>'floatval',
			'sort'=>'intval',
		);
	}
}