<?php


namespace Sm\Modules\Query\Sql\Event;


class BatchExecutionEvent extends SqlQueryExecutionEvent {
	protected $executionEvents = [];

	public function jsonSerialize() {
		return array_merge(parent::jsonSerialize(),
		                   [
			                   'queries'           => $this->query,
			                   'result'            => $this->executionSuccess,
			                   'formatted_queries' => $this->formatted_query,
			                   'query_variables'   => $this->query_variables,
			                   'exception'         => $this->exception,
		                   ]);
	}
	public function getExecutionEvents(): array {
		return $this->executionEvents;
	}
	public function setExecutionEvents(array $executionEvents) {
		$this->executionEvents = $executionEvents;
		$all_queries           = [];
		$all_db_handles        = [];
		$all_formatted_queries = [];
		$all_success           = [];
		$exception             = null;
		foreach ($executionEvents as $event) {
			$all_queries[]           = $event->getQuery();
			$all_db_handles[]        = $event->getDatabaseHandle();
			$all_stmt_handles[]      = $event->getStatementHandle();
			$all_formatted_queries[] = $event->getFormattedQuery();
			$all_success[]           = $event->getExecutionSuccess();
			$exception               = $event->getException();
			if ($exception) break;
		}

		$this->setQuery($all_queries);
		$this->setFormattedQuery($all_formatted_queries);
		$this->setDatabaseHandle($all_db_handles);
		$this->setStatementHandle($all_db_handles);
		$this->setExecutionSuccess(count(array_unique($all_success)) === 1);
		if ($exception instanceof \Throwable) $this->setException($exception);

		return $this;
	}
}