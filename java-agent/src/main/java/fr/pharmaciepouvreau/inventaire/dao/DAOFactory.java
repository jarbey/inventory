package fr.pharmaciepouvreau.inventaire.dao;

import org.springframework.context.ApplicationContext;
import org.springframework.context.support.ClassPathXmlApplicationContext;

public final class DAOFactory {

	/**
	 * name of the files used
	 */
	public static final String SPRING_FILENAME_API = "applicationContext-Dao.xml";

	/**
	 * singleton element servicesFactory
	 */
	private static DAOFactory services = null;
	private ApplicationContext ctx = null;

	private DAOFactory() {
		super();
		ctx = new ClassPathXmlApplicationContext(
				new String[] { DAOFactory.SPRING_FILENAME_API });

	}

	public static synchronized DAOFactory getServices() {
		if (DAOFactory.services == null) {
			DAOFactory.services = new DAOFactory();
		}
		return DAOFactory.services;
	}

	public static LGPIDao getLGPIDao() {
		return (LGPIDao) DAOFactory.getServices().ctx.getBean("LGPIDao");
	}

}
